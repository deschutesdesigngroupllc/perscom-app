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

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:position') || optional($user)->tokenCan('view:position');
    }

    public function view(?User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'view:position') || optional($user)->tokenCan('view:position');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:position') || optional($user)->tokenCan('create:position');
    }

    public function update(?User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'update:position') || optional($user)->tokenCan('update:position');
    }

    public function delete(?User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || optional($user)->tokenCan('delete:position');
    }

    public function restore(?User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || optional($user)->tokenCan('delete:position');
    }

    public function forceDelete(?User $user, Position $position): bool
    {
        return $this->hasPermissionTo($user, 'delete:position') || optional($user)->tokenCan('delete:position');
    }
}
