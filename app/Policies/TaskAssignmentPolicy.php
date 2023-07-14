<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class TaskAssignmentPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @return false|void
     */
    public function before()
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('view', $taskAssignment->task ?? new Task()) || $taskAssignment->user?->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Gate::check('create', Task::class);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('update', $taskAssignment->task ?? new Task());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('delete', $taskAssignment->task ?? new Task());
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('restore', $taskAssignment->task ?? new Task());
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('forceDelete', $taskAssignment->task ?? new Task());
    }
}
