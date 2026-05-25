<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Patient $patient): bool
    {
        // Admin and coordinator can view all
        if ($user->hasRole(['admin', 'coordinator'])) {
            return true;
        }

        // Doctor can view patients they supervise
        if ($user->isDoctor()) {
            return Assignment::where('patient_id', $patient->id)
                ->where('doctor_id', $user->id)
                ->exists();
        }

        // COAS can only view assigned patients
        if ($user->isCoas()) {
            return Assignment::where('patient_id', $patient->id)
                ->where('coas_id', $user->id)
                ->whereIn('status', ['pending', 'active'])
                ->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'doctor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->hasRole(['admin', 'coordinator']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Patient $patient): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Patient $patient): bool
    {
        return $user->hasRole(['admin']);
    }
}
