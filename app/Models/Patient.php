<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "user_id",
        "nik",
        "whatsapp_number",
        "medical_record_number",
        "initials",
        "age_category",
        "gender",
        "working_diagnosis",
        "care_context",
        "address",
        "consent_granted",
        "consent_timestamp",
    ];

    /**
     * The attributes that should be cast.
     * Sensitive fields are encrypted for HIPAA/UU PDP compliance.
     */
    protected $casts = [
        "nik" => "encrypted",
        "whatsapp_number" => "encrypted",
        "medical_record_number" => "encrypted",
        "working_diagnosis" => "encrypted",
        "address" => "encrypted",
        "consent_granted" => "boolean",
        "consent_timestamp" => "datetime",
    ];

    /**
     * The attributes hidden from serialization (for API responses).
     */
    protected $hidden = [
        "nik",
        "whatsapp_number",
        "medical_record_number",
        "address",
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PatientLog::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class);
    }

    public function activeAssignment(): ?Assignment
    {
        return $this->assignments()
            ->whereIn("status", ["pending", "active"])
            ->with("coas")
            ->first();
    }

    // Accessors for anonymized display
    public function getDisplayNameAttribute(): string
    {
        $initials = $this->initials ?? "P";
        return $initials . "***" . substr($initials, -1);
    }

    public function getMaskedNikAttribute(): ?string
    {
        if (!$this->nik) {
            return null;
        }
        $decrypted = $this->nik;
        return substr($decrypted, 0, 4) . "****" . substr($decrypted, -4);
    }

    public function getMaskedWhatsAppAttribute(): ?string
    {
        if (!$this->whatsapp_number) {
            return null;
        }
        $decrypted = $this->whatsapp_number;
        return substr($decrypted, 0, 4) . "****" . substr($decrypted, -4);
    }

    // Scope for active patients
    public function scopeActive($query)
    {
        return $query->whereHas("logs", function ($q) {
            $q->where("status", "!=", "approved");
        });
    }

    // Relationship to assignments (new)
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
