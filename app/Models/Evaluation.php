<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_log_id',
        'evaluator_id',
        'status',
        'feedback',
        'ratings',
        'evaluated_at',
    ];

    protected $casts = [
        'ratings' => 'array',
        'evaluated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_APPROVED = 'approved';
    const STATUS_REVISION_REQUESTED = 'revision_requested';
    const STATUS_REJECTED = 'rejected';

    // Rating labels
    const RATING_LABELS = [
        1 => 'Needs Improvement',
        2 => 'Developing',
        3 => 'Competent',
        4 => 'Proficient',
        5 => 'Exemplary',
    ];

    // Relationships
    public function clinicalLog(): BelongsTo
    {
        return $this->belongsTo(ClinicalLog::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeNeedsRevision($query)
    {
        return $query->where('status', self::STATUS_REVISION_REQUESTED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // Helpers
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRevisionRequested(): bool
    {
        return $this->status === self::STATUS_REVISION_REQUESTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getAverageRating(): ?float
    {
        if (empty($this->ratings)) {
            return null;
        }

        return array_sum($this->ratings) / count($this->ratings);
    }

    public function approve(string $feedback = null, array $ratings = []): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'feedback' => $feedback,
            'ratings' => $ratings,
            'evaluated_at' => now(),
        ]);
    }

    public function requestRevision(string $feedback): void
    {
        $this->update([
            'status' => self::STATUS_REVISION_REQUESTED,
            'feedback' => $feedback,
            'evaluated_at' => now(),
        ]);
    }

    public function reject(string $feedback): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'feedback' => $feedback,
            'evaluated_at' => now(),
        ]);
    }

    public static function getRatingLabels(): array
    {
        return self::RATING_LABELS;
    }
}
