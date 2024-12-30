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
        return $user->can('absence_view_any');
    }

    /**
     * Determine whether the user can view a specific Absence model.
     */
    public function view(User $user, Absence $absence): bool
    {
        return $user->can('absence_view') ||
            ($user->can('absence_view_own') && $absence->person_id === $user->person->id);
    }

    /**
     * Determine whether the user can create Absence models.
     */
    public function create(User $user): bool
    {
        return $user->can('absence_create');
    }

    /**
     * Determine whether the user can update a specific Absence model.
     */
    public function update(User $user, Absence $absence): bool
    {
        return $user->can('absence_update') ||
            ($user->person->id === $absence->person_id && $absence->status === AbsenceStatus::Requested->value);
    }

    /**
     * Determine whether the user can delete a specific Absence model.
     */
    public function delete(User $user, Absence $absence): bool
    {
        return $user->can('absence_delete') ||
            ($user->person->id === $absence->person_id && $absence->status === AbsenceStatus::Requested->value);
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
