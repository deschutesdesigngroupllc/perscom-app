<?php

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class UnitPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:unit') || $user?->tokenCan('view:unit');
    }

    public function view(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'view:unit') || $user?->tokenCan('view:unit');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:unit') || $user?->tokenCan('create:user');
    }

    public function update(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'update:unit') || $user?->tokenCan('update:user');
    }

    public function delete(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user?->tokenCan('delete:user');
    }

    public function restore(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user?->tokenCan('delete:user');
    }

    public function forceDelete(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || $user?->tokenCan('delete:user');
    }
}
