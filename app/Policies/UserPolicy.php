<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('user_view_any');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('user_view');
    }

    public function create(User $user): bool
    {
        return $user->can('user_create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('user_update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('user_delete');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('user_restore');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('user_force_delete');
    }
}
