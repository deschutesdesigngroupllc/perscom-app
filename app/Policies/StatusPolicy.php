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

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:status') || $user?->tokenCan('view:status');
    }

    public function view(User $user = null, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'view:status') || $user?->tokenCan('view:status');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:status') || $user?->tokenCan('create:status');
    }

    public function update(User $user = null, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'update:status') || $user?->tokenCan('update:status');
    }

    public function delete(User $user = null, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user?->tokenCan('delete:status');
    }

    public function restore(User $user = null, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user?->tokenCan('delete:status');
    }

    public function forceDelete(User $user = null, Status $status): bool
    {
        return $this->hasPermissionTo($user, 'delete:status') || $user?->tokenCan('delete:status');
    }
}
