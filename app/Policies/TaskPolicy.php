<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class TaskPolicy extends Policy
{
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
    public function viewAny(User $user = null)
    {
        return $this->hasPermissionTo($user, 'view:task') || $user?->tokenCan('view:task');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user = null, Task $task)
    {
        return $this->hasPermissionTo($user, 'view:task') ||
               $user?->tokenCan('view:task') ||
               $task->users->contains($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user = null)
    {
        return $this->hasPermissionTo($user, 'create:task') || $user?->tokenCan('create:task');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user = null, Task $task)
    {
        return $this->hasPermissionTo($user, 'update:task') || $user?->tokenCan('update:task');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user = null, Task $task)
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user?->tokenCan('delete:task');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user = null, Task $task)
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user?->tokenCan('delete:task');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user = null, Task $task)
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user?->tokenCan('delete:task');
    }

    /**
     * Determine where the user can attach to the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function attachAnyUser(User $user = null, Task $task)
    {
        return $this->update($user, $task);
    }

    /**
     * Determine where the user can attach to the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function attachUser(User $user = null, Task $task)
    {
        return $this->update($user, $task);
    }

    /**
     * Determine where the user can attach to the model.
     *
     * @return bool|\Illuminate\Auth\Access\Response
     */
    public function detachUser(User $user = null, Task $task)
    {
        return $this->update($user, $task);
    }
}
