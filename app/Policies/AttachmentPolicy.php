<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Attachment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AttachmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_attachment');
    }

    public function view(AuthUser $authUser, Attachment $attachment): bool
    {
        return $authUser->can('view_attachment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_attachment');
    }

    public function update(AuthUser $authUser, Attachment $attachment): bool
    {
        return $authUser->can('update_attachment');
    }

    public function delete(AuthUser $authUser, Attachment $attachment): bool
    {
        return $authUser->can('delete_attachment');
    }

    public function restore(AuthUser $authUser, Attachment $attachment): bool
    {
        return $authUser->can('restore_attachment');
    }

    public function forceDelete(AuthUser $authUser, Attachment $attachment): bool
    {
        return $authUser->can('force_delete_attachment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_attachment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_attachment');
    }

    public function replicate(AuthUser $authUser, Attachment $attachment): bool
    {
        return $authUser->can('replicate_attachment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_attachment');
    }
}
