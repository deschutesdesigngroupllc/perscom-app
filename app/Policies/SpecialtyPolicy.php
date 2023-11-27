<?php

namespace App\Policies;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class SpecialtyPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:specialty') || optional($user)->tokenCan('view:specialty');
    }

    public function view(User $user = null, Specialty $mos): bool
    {
        return $this->hasPermissionTo($user, 'view:specialty') || optional($user)->tokenCan('view:specialty');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:specialty') || optional($user)->tokenCan('create:specialty');
    }

    public function update(User $user = null, Specialty $mos): bool
    {
        return $this->hasPermissionTo($user, 'update:specialty') || optional($user)->tokenCan('update:specialty');
    }

    public function delete(User $user = null, Specialty $mos): bool
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || optional($user)->tokenCan('delete:specialty');
    }

    public function restore(User $user = null, Specialty $mos): bool
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || optional($user)->tokenCan('delete:specialty');
    }

    public function forceDelete(User $user = null, Specialty $mos): bool
    {
        return $this->hasPermissionTo($user, 'delete:specialty') || optional($user)->tokenCan('delete:specialty');
    }
}
