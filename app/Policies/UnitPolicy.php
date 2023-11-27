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

        return null;
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:unit') || optional($user)->tokenCan('view:unit');
    }

    public function view(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'view:unit') || optional($user)->tokenCan('view:unit');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:unit') || optional($user)->tokenCan('create:user');
    }

    public function update(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'update:unit') || optional($user)->tokenCan('update:user');
    }

    public function delete(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || optional($user)->tokenCan('delete:user');
    }

    public function restore(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || optional($user)->tokenCan('delete:user');
    }

    public function forceDelete(User $user = null, Unit $unit): bool
    {
        return $this->hasPermissionTo($user, 'delete:unit') || optional($user)->tokenCan('delete:user');
    }
}
