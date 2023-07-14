<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class PermissionPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:permission') || $user->tokenCan('view:permission');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Permission $permission): bool
    {
        return $this->hasPermissionTo($user, 'view:permission') || $user->tokenCan('view:permission');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:permission') || $user->tokenCan('create:permission');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:permission') || $user->tokenCan('update:permission');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user->tokenCan('delete:permission');
    }

    /**
     * Determine whether the user can detach the model.
     */
    public function detachRole(User $user, Permission $permission, Role $role): bool
    {
        if ($permission->is_application_permission && $role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:permission');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user->tokenCan('delete:permission');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user->tokenCan('delete:permission');
    }
}
