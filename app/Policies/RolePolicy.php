<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class RolePolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest() || Request::isDemoMode()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:role') || $user?->tokenCan('view:role');
    }

    public function view(User $user = null, Role $role): bool
    {
        return $this->hasPermissionTo($user, 'view:role') || $user?->tokenCan('view:role');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:role') || $user?->tokenCan('create:role');
    }

    public function update(User $user = null, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role') || $user?->tokenCan('update:role');
    }

    public function delete(User $user = null, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user?->tokenCan('delete:role');
    }

    public function detachPermission(User $user = null, Role $role, Permission $permission): bool
    {
        if ($role->is_application_role && $permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role');
    }

    public function restore(User $user = null, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user?->tokenCan('delete:role');
    }

    public function forceDelete(User $user = null, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || $user?->tokenCan('delete:role');
    }
}
