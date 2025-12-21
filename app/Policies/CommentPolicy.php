<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CommentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_comment');
    }

    public function view(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('view_comment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_comment');
    }

    public function update(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('update_comment');
    }

    public function delete(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('delete_comment');
    }

    public function restore(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('restore_comment');
    }

    public function forceDelete(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('force_delete_comment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_comment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_comment');
    }

    public function replicate(AuthUser $authUser, Comment $comment): bool
    {
        return $authUser->can('replicate_comment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_comment');
    }
}
