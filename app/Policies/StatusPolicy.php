<?php

namespace App\Policies;

use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class StatusPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:status') || optional($user)->tokenCan('view:status');
    }

    public function view(?User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'view:status') || optional($user)->tokenCan('view:status');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:status') || optional($user)->tokenCan('create:status');
    }

    public function update(?User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'update:status') || optional($user)->tokenCan('update:status');
    }

    public function delete(?User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || optional($user)->tokenCan('delete:status');
    }

    public function restore(?User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || optional($user)->tokenCan('delete:status');
    }

    public function forceDelete(?User $user, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || optional($user)->tokenCan('delete:status');
    }
}
