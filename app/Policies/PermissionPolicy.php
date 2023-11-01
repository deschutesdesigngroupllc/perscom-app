<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class PermissionPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest() || Request::isDemoMode()) {
            return false;
        }

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:permission') || $user?->tokenCan('view:permission');
    }

    public function view(User $user = null, Permission $permission): bool
    {
        return $this->hasPermissionTo($user, 'view:permission') || $user?->tokenCan('view:permission');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:permission') || $user?->tokenCan('create:permission');
    }

    public function update(User $user = null, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:permission') || $user?->tokenCan('update:permission');
    }

    public function delete(User $user = null, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user?->tokenCan('delete:permission');
    }

    public function detachRole(User $user = null, Permission $permission, Role $role): bool
    {
        if ($permission->is_application_permission && $role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:permission');
    }

    public function restore(User $user = null, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user?->tokenCan('delete:permission');
    }

    public function forceDelete(User $user = null, Permission $permission): bool
    {
        if ($permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:permission') || $user?->tokenCan('delete:permission');
    }
}
