<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'direction',
        'body',
        'is_sensitive',
        'is_emergency',
        'received_at',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'body' => 'encrypted',
        'is_sensitive' => 'boolean',
        'is_emergency' => 'boolean',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Direction constants
    const DIRECTION_INBOUND = 'inbound';
    const DIRECTION_OUTBOUND = 'outbound';

    // Emergency keywords (Indonesian)
    public const EMERGENCY_KEYWORDS = [
        'darurat',
        'urgent',
        'minta tolong',
        'tidak bisa bernapas',
        'nyeri dada',
        'pendarahan',
        'pingsan',
        'sakit sekali',
        'kritis',
        'emergency',
        'help',
        'tidak sadar',
        'kejang',
    ];

    // Relationships
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->assignment->patient();
    }

    // Scopes
    public function scopeInbound($query)
    {
        return $query->where('direction', self::DIRECTION_INBOUND);
    }

    public function scopeOutbound($query)
    {
        return $query->where('direction', self::DIRECTION_OUTBOUND);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_at');
    }

    // Helpers
    public function isInbound(): bool
    {
        return $this->direction === self::DIRECTION_INBOUND;
    }

    public function isOutbound(): bool
    {
        return $this->direction === self::DIRECTION_OUTBOUND;
    }

    public function markAsProcessed(): void
    {
        $this->update(['processed_at' => now()]);
    }

    public static function detectEmergency(string $message): bool
    {
        $lowercaseMessage = strtolower($message);

        foreach (self::EMERGENCY_KEYWORDS as $keyword) {
            if (str_contains($lowercaseMessage, strtolower($keyword))) {
                return true;
            }
        }

        return false;
    }

    public static function containsSensitiveKeywords(string $message): bool
    {
        $sensitivePatterns = [
            '/\b(\d{16})\b/', // 16-digit NIK
            '/\b(\d{10,15})\b/', // Phone numbers
            '/ktp/i',
            '/bpjs/i',
        ];

        foreach ($sensitivePatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }

    // Get decrypted body for display
    public function getDecryptedBody(): string
    {
        return $this->getAttributes()['body'] ?? '';
    }
}
