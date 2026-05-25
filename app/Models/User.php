<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = ["name", "email", "password"];

    protected $hidden = ["password", "remember_token"];

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    // Relationships
    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RotationAssignment::class, "user_id");
    }

    public function supervisedAssignments(): HasMany
    {
        return $this->hasMany(RotationAssignment::class, "supervisor_id");
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, "user_id");
    }

    public function patientLogs(): HasMany
    {
        return $this->hasMany(PatientLog::class, "user_id");
    }

    // Role check accessors
    public function isAdmin(): bool
    {
        return $this->hasRole("admin") ||
            $this->hasRole("Administrator Fakultas");
    }

    public function isDoctor(): bool
    {
        return $this->hasRole("doctor") || $this->hasRole("Dosen Pembimbing");
    }

    public function isCoas(): bool
    {
        return $this->hasRole("coas") || $this->hasRole("Koas");
    }

    public function isCoordinator(): bool
    {
        return $this->hasRole("coordinator") ||
            $this->hasRole("Koordinator Program");
    }

    public function getDashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return "admin.dashboard";
        }
        if ($this->isCoordinator()) {
            return "coordinator.dashboard";
        }
        if ($this->isDoctor()) {
            return "dosen.dashboard";
        }
        return "koas.dashboard";
    }
}
