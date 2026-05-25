<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MetaCloudProvider implements WhatsAppProviderInterface
{
    private string $accessToken;
    private string $phoneNumberId;
    private string $apiVersion;
    private string $graphUrl;

    public function __construct()
    {
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->apiVersion = config('services.whatsapp.api_version', 'v18.0');
        $this->graphUrl = 'https://graph.facebook.com';
    }

    /**
     * Send a text message via WhatsApp.
     */
    public function sendMessage(string $to, string $message): array
    {
        $url = "{$this->graphUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message,
            ],
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(30)
                ->post($url, $payload);

            $data = $response->json();

            if ($response->failed()) {
                Log::error('WhatsApp send failed', [
                    'error' => $data,
                    'to' => $to,
                ]);
                return [
                    'success' => false,
                    'error' => $data['error']['message'] ?? 'Unknown error',
                    'error_code' => $data['error']['code'] ?? null,
                ];
            }

            Log::info('WhatsApp message sent', [
                'to' => $to,
                'message_id' => $data['messages'][0]['id'] ?? null,
            ]);

            return [
                'success' => true,
                'message_id' => $data['messages'][0]['id'] ?? null,
                'status' => $data['messages'][0]['status'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception', [
                'error' => $e->getMessage(),
                'to' => $to,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a template message.
     */
    public function sendTemplate(string $to, string $templateName, array $variables = []): array
    {
        $url = "{$this->graphUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $components = [];

        // Add body variables if provided
        if (!empty($variables)) {
            $components[] = [
                'type' => 'body',
                'parameters' => array_map(function ($value) {
                    return [
                        'type' => 'text',
                        'text' => $value,
                    ];
                }, array_values($variables)),
            ];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $this->formatPhoneNumber($to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => 'id',
                ],
            ],
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(30)
                ->post($url, $payload);

            $data = $response->json();

            if ($response->failed()) {
                Log::error('WhatsApp template send failed', [
                    'error' => $data,
                    'template' => $templateName,
                    'to' => $to,
                ]);
                return [
                    'success' => false,
                    'error' => $data['error']['message'] ?? 'Unknown error',
                ];
            }

            return [
                'success' => true,
                'message_id' => $data['messages'][0]['id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp template send exception', [
                'error' => $e->getMessage(),
                'template' => $templateName,
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify Meta webhook signature.
     */
    public function verifySignature(string $payload, string $signature): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $this->accessToken);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Parse incoming webhook payload from Meta.
     */
    public function parseWebhookPayload(array $payload): array
    {
        $entry = $payload['entry'][0] ?? [];
        $changes = $entry['changes'][0] ?? [];
        $value = $changes['value'] ?? [];

        $messages = $value['messages'] ?? [];

        if (empty($messages)) {
            return [
                'valid' => false,
                'messages' => [],
            ];
        }

        $parsedMessages = [];
        foreach ($messages as $message) {
            $parsedMessages[] = [
                'message_id' => $message['id'] ?? null,
                'from' => $message['from'] ?? null,
                'body' => $message['text']['body'] ?? $message['caption'] ?? '',
                'timestamp' => $message['timestamp'] ?? null,
                'type' => $message['type'] ?? 'text',
            ];
        }

        return [
            'valid' => true,
            'messages' => $parsedMessages,
            'phone_number_id' => $value['metadata']['phone_number_id'] ?? null,
        ];
    }

    /**
     * Set webhook URL for incoming messages.
     */
    public function setWebhook(string $url): bool
    {
        $url = "{$this->graphUrl}/{$this->apiVersion}/{$this->phoneNumberId}/webhooks";

        try {
            $response = Http::withToken($this->accessToken)
                ->post($url, [
                    'webhook_url' => $url,
                    'verify_token' => config('services.whatsapp.verify_token'),
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook setup failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get webhook verification token.
     */
    public function getWebhookVerifyToken(): string
    {
        return config('services.whatsapp.verify_token', '');
    }

    /**
     * Format phone number to E.164 format.
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-digits
        $phone = preg_replace('/\D/', '', $phone);

        // Add country code if not present (Indonesia: 62)
        if (substr($phone, 0, 1) !== '+') {
            if (substr($phone, 0, 2) === '62') {
                $phone = $phone;
            } elseif (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            } else {
                $phone = '62' . $phone;
            }
        }

        return $phone;
    }

    /**
     * Check if phone number is valid.
     */
    public function isValidPhoneNumber(string $phone): bool
    {
        $phone = preg_replace('/\D/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    /**
     * Get message status.
     */
    public function getMessageStatus(string $messageId): ?array
    {
        $url = "{$this->graphUrl}/{$this->apiVersion}/{$messageId}";

        try {
            $response = Http::withToken($this->accessToken)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp message status check failed', [
                'error' => $e->getMessage(),
                'message_id' => $messageId,
            ]);
        }

        return null;
    }
}
