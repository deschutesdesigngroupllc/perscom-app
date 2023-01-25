<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class TaskAssignmentPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('view', $taskAssignment->task) || $taskAssignment->user?->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return Gate::check('create', Task::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('update', $taskAssignment->task);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, TaskAssignment $taskAssignment)
    {
        return Gate::check('delete', $taskAssignment->task);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, TaskAssignment $taskAssignment)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, TaskAssignment $taskAssignment)
    {
        //
    }
}
