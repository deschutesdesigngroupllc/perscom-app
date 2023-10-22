<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class PositionPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:position') || $user?->tokenCan('view:position');
    }

    public function view(User $user = null, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'view:position') || $user?->tokenCan('view:position');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:position') || $user?->tokenCan('create:position');
    }

    public function update(User $user = null, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'update:position') || $user?->tokenCan('update:position');
    }

    public function delete(User $user = null, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user?->tokenCan('delete:position');
    }

    public function restore(User $user = null, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user?->tokenCan('delete:position');
    }

    public function forceDelete(User $user = null, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || $user?->tokenCan('delete:position');
    }
}
