<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\App;

class GroupPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || optional($user)->tokenCan('view:group');
    }

    public function view(?User $user, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'view:group') || optional($user)->tokenCan('view:group');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:group') || optional($user)->tokenCan('create:group');
    }

    public function update(?User $user, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'update:group') || optional($user)->tokenCan('update:group');
    }

    public function delete(?User $user, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || optional($user)->tokenCan('delete:group');
    }

    public function restore(?User $user, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || optional($user)->tokenCan('delete:group');
    }

    public function forceDelete(?User $user, Group $group): bool
    {
        return $this->hasPermissionTo($user, 'delete:group') || optional($user)->tokenCan('delete:group');
    }
}
