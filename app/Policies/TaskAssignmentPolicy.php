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
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('view', $taskAssignment->task ?? new Task()) || $taskAssignment->user?->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return Gate::check('create', Task::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('update', $taskAssignment->task ?? new Task());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('delete', $taskAssignment->task ?? new Task());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('delete', $taskAssignment->task ?? new Task());
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('delete', $taskAssignment->task ?? new Task());
    }
}
