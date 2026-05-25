<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ClinicalLogAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_log_id',
        'filename',
        'original_filename',
        'mime_type',
        'size',
        'path',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    // Relationships
    public function clinicalLog(): BelongsTo
    {
        return $this->belongsTo(ClinicalLog::class);
    }

    // Helpers
    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getSizeFormattedAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    public static function allowedMimeTypes(): array
    {
        return [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg',
        ];
    }

    public static function maxSize(): int
    {
        return 5 * 1024 * 1024; // 5MB
    }
}
