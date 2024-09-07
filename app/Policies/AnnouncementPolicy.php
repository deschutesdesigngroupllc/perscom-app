<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\App;

class AnnouncementPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:announcement') || optional($user)->tokenCan('view:announcement');
    }

    public function view(?User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'view:announcement') || optional($user)->tokenCan('view:announcement');
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:announcement') || optional($user)->tokenCan('create:announcement');
    }

    public function update(?User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'update:announcement') || optional($user)->tokenCan('update:announcement');
    }

    public function delete(?User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || optional($user)->tokenCan('delete:announcement');
    }

    public function restore(?User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || optional($user)->tokenCan('delete:announcement');
    }

    public function forceDelete(?User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || optional($user)->tokenCan('delete:announcement');
    }
}
