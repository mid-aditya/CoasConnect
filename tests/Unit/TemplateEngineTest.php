<?php

namespace Tests\Unit;

use App\Services\WhatsApp\TemplateEngine;
use PHPUnit\Framework\TestCase;

class TemplateEngineTest extends TestCase
{
    private TemplateEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new TemplateEngine();
    }

    /**
     * Test basic variable substitution.
     */
    public function test_renders_variables(): void
    {
        $template = 'Halo {{name}}, selamat {{time}}!';
        $variables = [
            'name' => 'Andi',
            'time' => 'pagi',
        ];

        $result = $this->engine->render($template, $variables);

        $this->assertEquals('Halo Andi, selamat pagi!', $result);
    }

    /**
     * Test missing variables are preserved.
     */
    public function test_preserves_missing_variables(): void
    {
        $template = 'Halo {{name}}, jadwal {{date}}';
        $variables = ['name' => 'Andi'];

        $result = $this->engine->render($template, $variables);

        $this->assertEquals('Halo Andi, jadwal {{date}}', $result);
    }

    /**
     * Test variable extraction.
     */
    public function test_extracts_variables(): void
    {
        $template = 'Hello {{name}}, your appointment is on {{date}} at {{time}}';

        $variables = $this->engine->extractVariables($template);

        $this->assertContains('name', $variables);
        $this->assertContains('date', $variables);
        $this->assertContains('time', $variables);
        $this->assertCount(3, $variables);
    }

    /**
     * Test template validation.
     */
    public function test_validates_template_syntax(): void
    {
        $validTemplate = 'Valid: {{variable1}} and {{variable2}}';
        $result = $this->engine->validate($validTemplate);

        $this->assertTrue($result['valid']);
        $this->assertEmpty($result['errors']);
    }

    /**
     * Test preview with sample data.
     */
    public function test_previews_with_defaults(): void
    {
        $template = 'Hello {{patient_name}}, your doctor is {{doctor_name}}';

        $preview = $this->engine->preview($template);

        $this->assertStringContainsString('Nama Pasien', $preview);
        $this->assertStringContainsString('Nama Dokter', $preview);
    }

    /**
     * Test message formatting.
     */
    public function test_formats_multiline_message(): void
    {
        $lines = [
            'Line 1',
            'Line 2',
            'Line 3',
        ];

        $result = $this->engine->formatMessage($lines);

        $this->assertEquals("Line 1\nLine 2\nLine 3", $result);
    }
}
