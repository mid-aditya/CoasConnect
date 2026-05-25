<?php

namespace App\Policies;

use App\Models\Evaluation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EvaluationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'doctor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Evaluation $evaluation): bool
    {
        // Doctor can view their own evaluations
        if ($user->isDoctor() && $evaluation->evaluator_id === $user->id) {
            return true;
        }

        // COAS can view evaluations on their own logs
        if ($user->isCoas() && $evaluation->clinicalLog->user_id === $user->id) {
            return true;
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
        return $user->isDoctor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Evaluation $evaluation): bool
    {
        // Only the evaluator can update (and only if revision requested)
        return $user->isDoctor() && $evaluation->evaluator_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Evaluation $evaluation): bool
    {
        return $user->hasRole(['admin']);
    }
}
