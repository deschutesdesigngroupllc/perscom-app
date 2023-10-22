<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class AnnouncementPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:announcement') || $user?->tokenCan('view:announcement');
    }

    public function view(User $user = null, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'view:announcement') || $user?->tokenCan('view:announcement');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:announcement') || $user?->tokenCan('create:announcement');
    }

    public function update(User $user = null, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'update:announcement') || $user?->tokenCan('update:announcement');
    }

    public function delete(User $user = null, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || $user?->tokenCan('delete:announcement');
    }

    public function restore(User $user = null, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || $user?->tokenCan('delete:announcement');
    }

    public function forceDelete(User $user = null, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || $user?->tokenCan('delete:announcement');
    }
}
