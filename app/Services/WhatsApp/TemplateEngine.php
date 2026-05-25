<?php

namespace App\Services\WhatsApp;

class TemplateEngine
{
    /**
     * Pattern for template variables: {{variable_name}}
     */
    private const VARIABLE_PATTERN = '/\{\{(\w+)\}\}/';

    /**
     * Render a template with variables.
     */
    public function render(string $template, array $variables = []): string
    {
        $rendered = preg_replace_callback(self::VARIABLE_PATTERN, function ($matches) use ($variables) {
            $variableName = $matches[1];
            return $variables[$variableName] ?? $matches[0];
        }, $template);

        return $rendered;
    }

    /**
     * Extract variable names from a template.
     */
    public function extractVariables(string $template): array
    {
        preg_match_all(self::VARIABLE_PATTERN, $template, $matches);
        return array_unique($matches[1]);
    }

    /**
     * Validate template syntax.
     */
    public function validate(string $template): array
    {
        $errors = [];
        $variables = $this->extractVariables($template);

        // Check for empty variable names
        if (preg_match('/\{\s*\}/', $template)) {
            $errors[] = 'Empty variable names are not allowed';
        }

        // Check for nested braces
        if (preg_match('/\{\{[^}]*\{\{[^}]*\}\}[^}]*\}\}/', $template)) {
            $errors[] = 'Nested variable braces are not allowed';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'variables' => $variables,
        ];
    }

    /**
     * Preview a template with sample data.
     */
    public function preview(string $template, array $sampleData = []): string
    {
        // Generate sample data for any missing variables
        $defaultData = [
            'patient_name' => 'Nama Pasien',
            'coas_name' => 'Nama COAS',
            'doctor_name' => 'Nama Dokter',
            'date' => now()->format('d/m/Y'),
            'time' => now()->format('H:i'),
            'medication' => 'Nama Obat',
            'dosage' => 'Dosis',
            'symptoms' => 'Gejala',
        ];

        $data = array_merge($defaultData, $sampleData);

        return $this->render($template, $data);
    }

    /**
     * Escape special characters for WhatsApp formatting.
     */
    public function escapeForWhatsApp(string $text): string
    {
        // Escape special markdown characters
        $text = str_replace('*', '\*', $text);
        $text = str_replace('_', '\_', $text);
        $text = str_replace('~', '\~', $text);
        $text = str_replace('`', '\`', $text);

        return $text;
    }

    /**
     * Create a formatted message with line breaks.
     */
    public function formatMessage(array $lines): string
    {
        return implode("\n", $lines);
    }
}
