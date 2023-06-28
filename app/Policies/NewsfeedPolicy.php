<?php

namespace App\Policies;

use App\Models\Newsfeed;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class NewsfeedPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user->tokenCan('manage:newsfeed');
    }
}
