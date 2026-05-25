<?php

namespace App\Services\WhatsApp;

use App\Models\Assignment;
use App\Models\Patient;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use App\Services\WhatsApp\MetaCloudProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WhatsAppService
{
    private MetaCloudProvider $provider;
    private TemplateEngine $templateEngine;

    public function __construct(MetaCloudProvider $provider, TemplateEngine $templateEngine)
    {
        $this->provider = $provider;
        $this->templateEngine = $templateEngine;
    }

    /**
     * Process incoming WhatsApp message.
     */
    public function processIncoming(array $payload): void
    {
        $parsed = $this->provider->parseWebhookPayload($payload);

        if (!$parsed['valid'] || empty($parsed['messages'])) {
            return;
        }

        foreach ($parsed['messages'] as $messageData) {
            $this->processMessage($messageData);
        }
    }

    /**
     * Process a single incoming message.
     */
    private function processMessage(array $messageData): void
    {
        $from = $messageData['from'];
        $body = $messageData['body'];
        $timestamp = $messageData['timestamp'];

        // Find patient by WhatsApp number
        $patient = Patient::where('whatsapp_number', $from)->first();

        if (!$patient) {
            $this->handleUnregisteredPatient($from, $body);
            return;
        }

        // Find active assignment
        $assignment = Assignment::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'active'])
            ->with(['coas', 'doctor'])
            ->first();

        if (!$assignment) {
            $this->sendNoActiveAssignmentMessage($from, $patient);
            return;
        }

        // Check for emergency keywords
        $isEmergency = WhatsAppMessage::detectEmergency($body);
        $isSensitive = WhatsAppMessage::containsSensitiveKeywords($body);

        // Store the message
        $message = WhatsAppMessage::create([
            'assignment_id' => $assignment->id,
            'direction' => WhatsAppMessage::DIRECTION_INBOUND,
            'body' => $body,
            'is_sensitive' => $isSensitive,
            'is_emergency' => $isEmergency,
            'received_at' => now()->setTimestamp($timestamp),
            'metadata' => [
                'message_id' => $messageData['message_id'] ?? null,
                'type' => $messageData['type'] ?? 'text',
            ],
        ]);

        // Send acknowledgment
        $this->sendAcknowledgment($from, $messageData['message_id']);

        // Dispatch events based on content
        if ($isEmergency) {
            $this->handleEmergency($assignment, $body);
        }

        // Handle symptom report or general message
        $this->handlePatientMessage($assignment, $body);

        // Mark message as processed
        $message->markAsProcessed();
    }

    /**
     * Handle unregistered patient.
     */
    private function handleUnregisteredPatient(string $from, string $body): void
    {
        $template = WhatsAppTemplate::where('code', 'welcome')->first();

        $welcomeMessage = $template?->content ??
            "Selamat datang! Silakan ketik NIK atau nomor registrasi RS Anda untuk verifikasi.";

        $this->sendMessage($from, $welcomeMessage);
    }

    /**
     * Handle message when patient has no active assignment.
     */
    private function handleNoActiveAssignmentMessage(string $from, Patient $patient): void
    {
        $message = "Halo {$patient->initials}! Anda telah diverifikasi. " .
            "Saat ini belum ada COAS yang ditugaskan. Tim kami akan segera menugaskan.";

        $this->sendMessage($from, $message);
    }

    /**
     * Send acknowledgment to patient.
     */
    private function sendAcknowledgment(string $to, ?string $messageId): void
    {
        // Meta Cloud API automatically sends delivery/read receipts
        // This can be used for custom acknowledgments if needed
    }

    /**
     * Handle emergency situation.
     */
    private function handleEmergency(Assignment $assignment, string $messageBody): void
    {
        // Notify COAS
        if ($assignment->coas) {
            Log::warning('EMERGENCY DETECTED', [
                'patient_id' => $assignment->patient_id,
                'coas_id' => $assignment->coas_id,
                'message' => $messageBody,
            ]);
            // TODO: Send notification to COAS via dashboard/in-app notification
        }

        // Notify doctor
        if ($assignment->doctor) {
            Log::warning('EMERGENCY DETECTED - DOCTOR NOTIFICATION', [
                'patient_id' => $assignment->patient_id,
                'doctor_id' => $assignment->doctor_id,
                'message' => $messageBody,
            ]);
            // TODO: Send urgent notification to doctor
        }

        // Send emergency acknowledgment to patient
        $emergencyMessage = "⚠️ PERHATIAN: Pesan Anda mengandung kata kunci darurat. " .
            "Tim medis akan segera menghubungi Anda. Mohon tetap tenang.";

        $this->sendMessage($assignment->patient->whatsapp_number, $emergencyMessage);
    }

    /**
     * Handle general patient message.
     */
    private function handlePatientMessage(Assignment $assignment, string $body): void
    {
        // Check if this looks like a symptom report
        if ($this->isSymptomReport($body)) {
            $this->sendSymptomAcknowledgment($assignment, $body);
        }
    }

    /**
     * Check if message is a symptom report.
     */
    private function isSymptomReport(string $body): bool
    {
        $symptomKeywords = [
            'sakit', 'nyeri', 'demam', 'batuk', 'pusing', 'mual',
            'gejala', 'kondisi', 'tidak enak', 'lemas'
        ];

        $lowercaseBody = strtolower($body);
        foreach ($symptomKeywords as $keyword) {
            if (str_contains($lowercaseBody, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Send symptom acknowledgment.
     */
    private function sendSymptomAcknowledgment(Assignment $assignment, string $body): void
    {
        $coasName = $assignment->coas?->name ?? 'Tim COAS';
        $message = "Terima kasih. Gejala Anda telah dicatat. " .
            "COAS {$coasName} akan meninjaunya shortly.";

        $this->sendMessage($assignment->patient->whatsapp_number, $message);
    }

    /**
     * Send a message using the configured provider.
     */
    public function sendMessage(string $to, string $message): array
    {
        return $this->provider->sendMessage($to, $message);
    }

    /**
     * Send a template message with variables.
     */
    public function sendTemplateMessage(string $to, string $templateCode, array $variables = []): array
    {
        $template = WhatsAppTemplate::where('code', $templateCode)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            Log::warning("WhatsApp template not found: {$templateCode}");
            return [
                'success' => false,
                'error' => 'Template not found',
            ];
        }

        $renderedContent = $this->templateEngine->render($template->content, $variables);

        return $this->provider->sendMessage($to, $renderedContent);
    }

    /**
     * Send welcome message to newly assigned patient.
     */
    public function sendWelcomeMessage(Patient $patient, Assignment $assignment): void
    {
        $coasName = $assignment->coas?->name ?? 'Tim COAS';

        $template = WhatsAppTemplate::where('code', 'welcome_assigned')->first();

        if ($template) {
            $this->sendTemplateMessage($patient->whatsapp_number, 'welcome_assigned', [
                'patient_name' => $patient->initials,
                'coas_name' => $coasName,
            ]);
        } else {
            $message = "Halo {$patient->initials}! Anda terhubung dengan COAS {$coasName}. " .
                "Kirimkan pesan kapan saja untuk konsultasi.";
            $this->sendMessage($patient->whatsapp_number, $message);
        }
    }

    /**
     * Send reminder message.
     */
    public function sendReminder(Patient $patient, string $reminderType, array $data = []): void
    {
        $templateCode = "reminder_{$reminderType}";

        $this->sendTemplateMessage($patient->whatsapp_number, $templateCode, $data);
    }

    /**
     * Verify webhook signature.
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        return $this->provider->verifySignature($payload, $signature);
    }

    /**
     * Handle webhook verification request.
     */
    public function handleWebhookVerification(string $mode, string $token, string $challenge): array
    {
        $verifyToken = config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            Log::info('WhatsApp webhook verified successfully');
            return [
                'success' => true,
                'challenge' => $challenge,
            ];
        }

        Log::warning('WhatsApp webhook verification failed', [
            'mode' => $mode,
            'token_match' => $token === $verifyToken,
        ]);

        return [
            'success' => false,
        ];
    }
}
