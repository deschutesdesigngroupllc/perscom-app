<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Announcement;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_announcement');
    }

    public function view(AuthUser $authUser, Announcement $announcement): bool
    {
        return $authUser->can('view_announcement');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_announcement');
    }

    public function update(AuthUser $authUser, Announcement $announcement): bool
    {
        return $authUser->can('update_announcement');
    }

    public function delete(AuthUser $authUser, Announcement $announcement): bool
    {
        return $authUser->can('delete_announcement');
    }

    public function restore(AuthUser $authUser, Announcement $announcement): bool
    {
        return $authUser->can('restore_announcement');
    }

    public function forceDelete(AuthUser $authUser, Announcement $announcement): bool
    {
        return $authUser->can('force_delete_announcement');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_announcement');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_announcement');
    }

    public function replicate(AuthUser $authUser, Announcement $announcement): bool
    {
        return $authUser->can('replicate_announcement');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_announcement');
    }
}
