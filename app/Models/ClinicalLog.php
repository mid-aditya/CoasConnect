<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'rotation_id',
        'user_id',
        'activity_date',
        'activity_type',
        'description',
        'patient_condition',
        'reflection',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // Activity types
    const TYPE_ANAMNESIS = 'anamnesis';
    const TYPE_PHYSICAL_EXAM = 'physical_exam';
    const TYPE_PROCEDURE = 'procedure';
    const TYPE_EDUCATION = 'education';
    const TYPE_CONSULTATION = 'consultation';
    const TYPE_OTHER = 'other';

    // Patient conditions
    const CONDITION_STABLE = 'stable';
    const CONDITION_IMPROVING = 'improving';
    const CONDITION_WORSENING = 'worsening';
    const CONDITION_CRITICAL = 'critical';

    // Status
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_REVISION_REQUESTED = 'revision_requested';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function rotation(): BelongsTo
    {
        return $this->belongsTo(Rotation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'clinical_log_competency')
            ->withPivot(['rating', 'feedback'])
            ->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ClinicalLogAttachment::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', self::STATUS_REVIEWED);
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function submit(): void
    {
        $this->update([
            'status' => self::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);
    }

    public static function getActivityTypes(): array
    {
        return [
            self::TYPE_ANAMNESIS => 'Anamnesis',
            self::TYPE_PHYSICAL_EXAM => 'Pemeriksaan Fisik',
            self::TYPE_PROCEDURE => 'Tindakan/Prosedur',
            self::TYPE_EDUCATION => 'Edukasi Pasien',
            self::TYPE_CONSULTATION => 'Konsultasi',
            self::TYPE_OTHER => 'Lainnya',
        ];
    }

    public static function getPatientConditions(): array
    {
        return [
            self::CONDITION_STABLE => 'Stabil',
            self::CONDITION_IMPROVING => 'Membaik',
            self::CONDITION_WORSENING => 'Memburuk',
            self::CONDITION_CRITICAL => 'Kritis',
        ];
    }
}
