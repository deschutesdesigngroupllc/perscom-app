<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class AnnouncementPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return false|void
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermissionTo($user, 'view:announcement') || $user->tokenCan('view:announcement');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'view:announcement') || $user->tokenCan('view:announcement');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:announcement') || $user->tokenCan('create:announcement');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'update:announcement') || $user->tokenCan('update:announcement');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || $user->tokenCan('delete:announcement');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || $user->tokenCan('delete:announcement');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return $this->hasPermissionTo($user, 'delete:announcement') || $user->tokenCan('delete:announcement');
    }
}
