<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\App;

class RolePolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin() || App::isDemo()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:role') || optional($user)->tokenCan('view:role');
    }

    public function view(?User $user, Role $role): bool
    {
        return $this->hasPermissionTo($user, 'view:role') || optional($user)->tokenCan('view:role');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:role') || optional($user)->tokenCan('create:role');
    }

    public function update(?User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role') || optional($user)->tokenCan('update:role');
    }

    public function delete(?User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || optional($user)->tokenCan('delete:role');
    }

    public function detachPermission(?User $user, Role $role, Permission $permission): bool
    {
        if ($role->is_application_role && $permission->is_application_permission) {
            return false;
        }

        return $this->hasPermissionTo($user, 'update:role');
    }

    public function restore(?User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || optional($user)->tokenCan('delete:role');
    }

    public function forceDelete(?User $user, Role $role): bool
    {
        if ($role->is_application_role) {
            return false;
        }

        return $this->hasPermissionTo($user, 'delete:role') || optional($user)->tokenCan('delete:role');
    }
}
