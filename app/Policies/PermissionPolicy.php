<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class PermissionPolicy extends Policy
{
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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user = null)
    {
        return $this->hasPermissionTo($user, 'view:permission') || $user?->tokenCan('view:permission');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Permission $permission)
    {
        return $this->hasPermissionTo($user, 'view:permission') || $user?->tokenCan('view:permission');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:permission') || $user?->tokenCan('create:permission');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Permission $permission)
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:permission') || $user?->tokenCan('update:permission');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Permission $permission)
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user?->tokenCan('delete:permission');
    }

    /**
     * Determine whether the user can detach the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function detachRole(User $user = null, Permission $permission, Role $role)
    {
        if ($permission->is_application_permission && $role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:permission');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Permission $permission)
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user?->tokenCan('delete:permission');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Permission $permission)
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user?->tokenCan('delete:permission');
    }
}
