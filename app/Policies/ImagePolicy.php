<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Image;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ImagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_image');
    }

    public function view(AuthUser $authUser, Image $image): bool
    {
        return $authUser->can('view_image');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_image');
    }

    public function update(AuthUser $authUser, Image $image): bool
    {
        return $authUser->can('update_image');
    }

    public function delete(AuthUser $authUser, Image $image): bool
    {
        return $authUser->can('delete_image');
    }

    public function restore(AuthUser $authUser, Image $image): bool
    {
        return $authUser->can('restore_image');
    }

    public function forceDelete(AuthUser $authUser, Image $image): bool
    {
        return $authUser->can('force_delete_image');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_image');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_image');
    }

    public function replicate(AuthUser $authUser, Image $image): bool
    {
        return $authUser->can('replicate_image');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_image');
    }
}
