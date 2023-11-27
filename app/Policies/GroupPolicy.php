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

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || optional($user)->tokenCan('view:group');
    }

    public function view(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || optional($user)->tokenCan('view:group');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:group') || optional($user)->tokenCan('create:user');
    }

    public function update(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'update:group') || optional($user)->tokenCan('update:user');
    }

    public function delete(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || optional($user)->tokenCan('delete:user');
    }

    public function restore(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || optional($user)->tokenCan('delete:user');
    }

    public function forceDelete(User $user = null, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || optional($user)->tokenCan('delete:user');
    }
}
