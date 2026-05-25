<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view assignments they are part of
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assignment $assignment): bool
    {
        // Admin and coordinator can view all
        if ($user->hasRole(['admin', 'coordinator'])) {
            return true;
        }

        // Doctor can view assignments they supervise
        if ($user->isDoctor()) {
            return $assignment->doctor_id === $user->id;
        }

        // COAS can view their own assignments
        if ($user->isCoas()) {
            return $assignment->coas_id === $user->id;
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
    public function update(User $user, Assignment $assignment): bool
    {
        // Only admin can update assignments
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can delete/terminate the model.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can complete the assignment.
     */
    public function complete(User $user, Assignment $assignment): bool
    {
        // Admin can complete any assignment
        if ($user->hasRole(['admin'])) {
            return true;
        }

        // Doctor can complete assignments they supervise
        if ($user->isDoctor()) {
            return $assignment->doctor_id === $user->id && $assignment->isActive();
        }

        return false;
    }

    /**
     * Determine whether the user can reassign the assignment.
     */
    public function reassign(User $user, Assignment $assignment): bool
    {
        return $user->hasRole(['admin', 'doctor']);
    }
}
