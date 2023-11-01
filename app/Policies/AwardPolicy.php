<?php

namespace App\Policies;

use App\Models\Award;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AwardPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:award') || $user?->tokenCan('view:award');
    }

    public function view(User $user = null, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'view:award') || $user?->tokenCan('view:award');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:award') || $user?->tokenCan('create:award');
    }

    public function update(User $user = null, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'update:award') || $user?->tokenCan('update:award');
    }

    public function delete(User $user = null, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user?->tokenCan('delete:award');
    }

    public function restore(User $user = null, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user?->tokenCan('delete:award');
    }

    public function forceDelete(User $user = null, Award $award): bool
    {
        return $this->hasPermissionTo($user, 'delete:award') || $user?->tokenCan('delete:award');
    }
}
