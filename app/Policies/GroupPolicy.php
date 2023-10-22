<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class GroupPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || $user?->tokenCan('view:group');
    }

    public function view(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || $user?->tokenCan('view:group');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:group') || $user?->tokenCan('create:user');
    }

    public function update(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'update:group') || $user?->tokenCan('update:user');
    }

    public function delete(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || $user?->tokenCan('delete:user');
    }

    public function restore(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || $user?->tokenCan('delete:user');
    }

    public function forceDelete(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || $user?->tokenCan('delete:user');
    }
}
