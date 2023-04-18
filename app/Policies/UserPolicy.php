<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user) {
        return $user->hasAnyPermission(['create: user', 'update: user', 'delete: user', 'read: user']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user) {
        return $user->hasAnyPermission(['create: user']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model) {
        return $user->hasAnyPermission(['update: user']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function delete(User $user, User $model) {
        return $user->hasAnyPermission(['delete: user']);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function restore(User $user, User $model) {
        return $user->hasAnyPermission(['update: user']);
    }
}
