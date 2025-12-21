<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MessagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_message');
    }

    public function view(AuthUser $authUser, Message $message): bool
    {
        return $authUser->can('view_message');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_message');
    }

    public function update(AuthUser $authUser, Message $message): bool
    {
        return $authUser->can('update_message');
    }

    public function delete(AuthUser $authUser, Message $message): bool
    {
        return $authUser->can('delete_message');
    }

    public function restore(AuthUser $authUser, Message $message): bool
    {
        return $authUser->can('restore_message');
    }

    public function forceDelete(AuthUser $authUser, Message $message): bool
    {
        return $authUser->can('force_delete_message');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_message');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_message');
    }

    public function replicate(AuthUser $authUser, Message $message): bool
    {
        return $authUser->can('replicate_message');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_message');
    }
}
