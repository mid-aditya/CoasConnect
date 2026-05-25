<?php

namespace Tests\Unit;

use App\Models\WhatsAppMessage;
use PHPUnit\Framework\TestCase;

class WhatsAppMessageTest extends TestCase
{
    /**
     * Test emergency keyword detection.
     */
    public function test_detects_emergency_keywords(): void
    {
        $this->assertTrue(WhatsAppMessage::detectEmergency('Saya butuh bantuan darurat!'));
        $this->assertTrue(WhatsAppMessage::detectEmergency('Panggil dokter, tidak bisa bernapas!'  ));
        $this->assertTrue(WhatsAppMessage::detectEmergency('nyeri dada hebat'));
        $this->assertTrue(WhatsAppMessage::detectEmergency('urgent'));
    }

    /**
     * Test non-emergency messages.
     */
    public function test_does_not_detect_emergency_in_normal_messages(): void
    {
        $this->assertFalse(WhatsAppMessage::detectEmergency('Terima kasih atas informasinya'));
        $this->assertFalse(WhatsAppMessage::detectEmergency('Kapan jadwal kontrol saya?'));
        $this->assertFalse(WhatsAppMessage::detectEmergency('Minum obat sudah dilakukan'));
    }

    /**
     * Test sensitive keyword detection.
     */
    public function test_detects_sensitive_keywords(): void
    {
        $this->assertTrue(WhatsAppMessage::containsSensitiveKeywords('NIK saya 1234567890123456'));
        $this->assertTrue(WhatsAppMessage::containsSensitiveKeywords('BPJS: 1234567890'));
        $this->assertTrue(WhatsAppMessage::containsSensitiveKeywords('Fotokopi KTP saya'));
    }

    /**
     * Test direction constants.
     */
    public function test_direction_constants(): void
    {
        $this->assertEquals('inbound', WhatsAppMessage::DIRECTION_INBOUND);
        $this->assertEquals('outbound', WhatsAppMessage::DIRECTION_OUTBOUND);
    }
}
