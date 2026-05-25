<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'coas_id',
        'doctor_id',
        'rotation_id',
        'status',
        'assigned_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_TERMINATED = 'terminated';

    // Relationships
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function coas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coas_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function rotation(): BelongsTo
    {
        return $this->belongsTo(Rotation::class);
    }

    public function clinicalLogs(): HasMany
    {
        return $this->hasMany(ClinicalLog::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function markAsActive(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'assigned_at' => now(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function markAsTerminated(string $reason = null): void
    {
        $this->update([
            'status' => self::STATUS_TERMINATED,
            'notes' => $reason ?? $this->notes,
        ]);
    }
}
