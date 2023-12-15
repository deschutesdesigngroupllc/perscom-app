<?php

namespace App\Policies;

use App\Models\Qualification;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class QualificationPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:qualification') || optional($user)->tokenCan('view:qualification');
    }

    public function view(?User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'view:qualification') || optional($user)->tokenCan('view:qualification');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:qualification') || optional($user)->tokenCan('create:qualification');
    }

    public function update(?User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'update:qualification') || optional($user)->tokenCan('update:qualification');
    }

    public function delete(?User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || optional($user)->tokenCan('delete:qualification');
    }

    public function restore(?User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || optional($user)->tokenCan('delete:qualification');
    }

    public function forceDelete(?User $user, Qualification $qualification): bool
    {
        return $this->hasPermissionTo($user, 'delete:qualification') || optional($user)->tokenCan('delete:qualification');
    }
}
