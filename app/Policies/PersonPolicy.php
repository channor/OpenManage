<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PersonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('person_view_any');
    }

    public function view(User $user, Person $person): bool
    {
        return $user->can('person_view');
    }

    public function create(User $user): bool
    {
        return $user->can('person_create');
    }

    public function update(User $user, Person $person): bool
    {
        return $user->can('person_update');
    }

    public function delete(User $user, Person $person): bool
    {
        return $user->can('person_delete');
    }

    public function restore(User $user, Person $person): bool
    {
        return $user->can('person_restore');
    }

    public function forceDelete(User $user, Person $person): bool
    {
        return $user->can('person_force_delete');
    }
}
