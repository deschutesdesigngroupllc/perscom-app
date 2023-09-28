<?php

namespace App\Policies;

use App\Models\NewsfeedItem;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class NewsfeedItemPolicy extends Policy
{
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
    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }
}
