<?php

namespace App\Policies;

use App\Models\NewsfeedItem;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class NewsfeedItemPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    public function view(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    public function update(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    public function delete(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    public function restore(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }

    public function forceDelete(User $user = null, NewsfeedItem $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || $user?->tokenCan('manage:newsfeed');
    }
}
