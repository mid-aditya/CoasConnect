<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function __construct(
        private WhatsAppService $whatsAppService
    ) {}

    /**
     * Handle webhook verification (GET request from Meta).
     */
    public function verify(Request $request): JsonResponse
    {
        $mode = $request->query('hub.mode');
        $token = $request->query('hub.verify_token');
        $challenge = $request->query('hub.challenge');

        $result = $this->whatsAppService->handleWebhookVerification($mode, $token, $challenge);

        if ($result['success']) {
            return response($challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /**
     * Handle incoming webhook (POST request from Meta).
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $payload = $request->all();

            Log::info('WhatsApp webhook received', [
                'payload' => $payload,
            ]);

            // Process the incoming message
            $this->whatsAppService->processIncoming($payload);

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return 200 to acknowledge receipt (Meta requires this)
            return response()->json(['status' => 'error'], 200);
        }
    }
}
