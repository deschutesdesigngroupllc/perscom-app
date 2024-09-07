<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Newsfeed;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class NewsfeedPolicy extends Policy
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
        return true;
    }

    public function view(?User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed')
            || optional($user)->tokenCan('manage:newsfeed')
            || Gate::check('view', $newsfeed->subject);
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed')
            || optional($user)->tokenCan('manage:newsfeed');
    }

    public function update(?User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || optional($user)->tokenCan('manage:newsfeed');
    }

    public function delete(?User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || optional($user)->tokenCan('manage:newsfeed');
    }

    public function restore(?User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || optional($user)->tokenCan('manage:newsfeed');
    }

    public function forceDelete(?User $user, Newsfeed $newsfeed): bool
    {
        return $this->hasPermissionTo($user, 'manage:newsfeed') || optional($user)->tokenCan('manage:newsfeed');
    }
}
