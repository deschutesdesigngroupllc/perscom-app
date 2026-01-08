<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Newsfeed;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class NewsfeedPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_newsfeed');
    }

    public function view(AuthUser $authUser, Newsfeed $newsfeed): bool
    {
        return $authUser->can('view_newsfeed');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_newsfeed');
    }

    public function update(AuthUser $authUser, Newsfeed $newsfeed): bool
    {
        return $authUser->can('update_newsfeed');
    }

    public function delete(AuthUser $authUser, Newsfeed $newsfeed): bool
    {
        return $authUser->can('delete_newsfeed');
    }

    public function restore(AuthUser $authUser, Newsfeed $newsfeed): bool
    {
        return $authUser->can('restore_newsfeed');
    }

    public function forceDelete(AuthUser $authUser, Newsfeed $newsfeed): bool
    {
        return $authUser->can('force_delete_newsfeed');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_newsfeed');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_newsfeed');
    }

    public function replicate(AuthUser $authUser, Newsfeed $newsfeed): bool
    {
        return $authUser->can('replicate_newsfeed');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_newsfeed');
    }
}
