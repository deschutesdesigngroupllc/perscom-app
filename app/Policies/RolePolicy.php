<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class RolePolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return false|void
     */
    public function before()
    {
        if (Request::isCentralRequest() || Request::isDemoMode()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'view:role') || $user->tokenCan('view:role');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $this->hasPermissionTo($user, 'view:role') || $user->tokenCan('view:role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:role') || $user->tokenCan('create:role');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role') || $user->tokenCan('update:role');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user->tokenCan('delete:role');
    }

    /**
     * Determine whether the user can detach the model.
     */
    public function detachPermission(User $user, Role $role, Permission $permission): bool
    {
        if ($role->is_application_role && $permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user->tokenCan('delete:role');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user->tokenCan('delete:role');
    }
}
