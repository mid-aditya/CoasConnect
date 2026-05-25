<?php

namespace App\Policies;

use App\Models\ClinicalLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClinicalLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClinicalLog $clinicalLog): bool
    {
        // COAS can view their own logs
        if ($user->isCoas() && $clinicalLog->user_id === $user->id) {
            return true;
        }

        // Doctor can view logs from their supervised COAS
        if ($user->isDoctor()) {
            return $clinicalLog->assignment->doctor_id === $user->id;
        }

        // Admin and coordinator can view all
        if ($user->hasRole(['admin', 'coordinator'])) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isCoas();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClinicalLog $clinicalLog): bool
    {
        // COAS can only update their own draft logs
        if ($user->isCoas()) {
            return $clinicalLog->user_id === $user->id
                && $clinicalLog->status === ClinicalLog::STATUS_DRAFT;
        }

        return false;
    }

    /**
     * Determine whether the user can submit the model for review.
     */
    public function submit(User $user, ClinicalLog $clinicalLog): bool
    {
        // COAS can only submit their own draft logs
        if ($user->isCoas()) {
            return $clinicalLog->user_id === $user->id
                && $clinicalLog->status === ClinicalLog::STATUS_DRAFT;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClinicalLog $clinicalLog): bool
    {
        // COAS can only delete their own draft logs
        if ($user->isCoas()) {
            return $clinicalLog->user_id === $user->id
                && $clinicalLog->status === ClinicalLog::STATUS_DRAFT;
        }

        // Admin can delete any draft log
        return $user->hasRole(['admin']);
    }

    /**
     * Determine whether the user can review the model.
     */
    public function review(User $user, ClinicalLog $clinicalLog): bool
    {
        // Only doctors can review logs
        if (!$user->isDoctor()) {
            return false;
        }

        // Doctor can only review logs from their supervised assignments
        return $clinicalLog->assignment->doctor_id === $user->id
            && $clinicalLog->status === ClinicalLog::STATUS_SUBMITTED;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, ClinicalLog $clinicalLog): bool
    {
        return $this->review($user, $clinicalLog);
    }

    /**
     * Determine whether the user can request revision.
     */
    public function requestRevision(User $user, ClinicalLog $clinicalLog): bool
    {
        return $this->review($user, $clinicalLog);
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, ClinicalLog $clinicalLog): bool
    {
        return $this->review($user, $clinicalLog);
    }
}
