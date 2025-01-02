<?php

namespace App\Policies;

use App\Enums\AbsenceStatus;
use App\Models\Absence;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AbsencePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any Absence models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAbsenceManager() || $user->can('absence_view_any');
    }

    /**
     * Determine whether the user can view a specific Absence model.
     */
    public function view(User $user, Absence $absence): bool
    {
        return $user->isAbsenceManager()
            || $absence->isOwnedBy($user)
            || $absence->canBeManagedBy($user);
    }

    /**
     * Determine whether the user can create Absence models.
     */
    public function create(User $user): bool
    {
        return $user->isAbsenceManager() || $user->can('absence_create');
    }

    /**
     * Determine whether the user can update a specific Absence model.
     */
    public function update(User $user, Absence $absence): bool
    {
        if($user->isAbsenceManager() || $absence->canBeManagedBy($user)) {
            return true;
        }

        return $absence->isOwnedBy($user) && $absence->status === AbsenceStatus::Requested;
    }

    /**
     * Determine whether the user can delete a specific Absence model.
     */
    public function delete(User $user, Absence $absence): bool
    {
        return $absence->canBeManagedBy($user) || $user->isAbsenceManager() ||
            ($absence->isOwnedBy($user) && $absence->status === AbsenceStatus::Requested);
    }

    /**
     * Determine whether the user can restore a specific Absence model.
     */
    public function restore(User $user, Absence $absence): bool
    {
        return $user->can('absence_restore');
    }

    /**
     * Determine whether the user can permanently delete a specific Absence model.
     */
    public function forceDelete(User $user, Absence $absence): bool
    {
        return $user->can('absence_force_delete');
    }
}
