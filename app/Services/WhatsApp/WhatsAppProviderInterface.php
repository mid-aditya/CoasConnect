<?php

namespace App\Services\WhatsApp;

interface WhatsAppProviderInterface
{
    /**
     * Send a text message.
     */
    public function sendMessage(string $to, string $message): array;

    /**
     * Send a template message.
     */
    public function sendTemplate(string $to, string $templateName, array $variables = []): array;

    /**
     * Verify webhook signature.
     */
    public function verifySignature(string $payload, string $signature): bool;

    /**
     * Parse incoming webhook payload.
     */
    public function parseWebhookPayload(array $payload): array;

    /**
     * Set webhook URL for incoming messages.
     */
    public function setWebhook(string $url): bool;

    /**
     * Get webhook verification token.
     */
    public function getWebhookVerifyToken(): ?string;
}
