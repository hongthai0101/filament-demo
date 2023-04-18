<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['create: permission', 'update: permission', 'delete: permission', 'read: permission']);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create: permission']);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Permission $permission
     * @return bool
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->hasAnyPermission(['update: permission']);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param  Permission  $permission
     * @return bool
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasAnyPermission(['delete: permission']);
    }
}
