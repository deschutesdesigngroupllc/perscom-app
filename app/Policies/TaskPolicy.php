<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_task');
    }

    public function view(AuthUser $authUser, Task $task): bool
    {
        return $authUser->can('view_task');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_task');
    }

    public function update(AuthUser $authUser, Task $task): bool
    {
        return $authUser->can('update_task');
    }

    public function delete(AuthUser $authUser, Task $task): bool
    {
        return $authUser->can('delete_task');
    }

    public function restore(AuthUser $authUser, Task $task): bool
    {
        return $authUser->can('restore_task');
    }

    public function forceDelete(AuthUser $authUser, Task $task): bool
    {
        return $authUser->can('force_delete_task');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_task');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_task');
    }

    public function replicate(AuthUser $authUser, Task $task): bool
    {
        return $authUser->can('replicate_task');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_task');
    }
}
