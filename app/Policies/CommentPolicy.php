<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class CommentPolicy extends Policy
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
        return optional($user)->hasRole('Admin');
    }

    public function view(?User $user, Comment $comment): bool
    {
        return Gate::check('view', $comment->commentable);
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:comment') || optional($user)->tokenCan('create:comment');
    }

    public function update(?User $user, Comment $comment): bool
    {
        return Gate::check('update', $comment->commentable);
    }

    public function delete(?User $user, Comment $comment): bool
    {
        return Gate::check('delete', $comment->commentable);
    }

    public function restore(?User $user, Comment $comment): bool
    {
        return Gate::check('restore', $comment->commentable);
    }

    public function forceDelete(?User $user, Comment $comment): bool
    {
        return Gate::check('forceDelete', $comment->commentable);
    }
}
