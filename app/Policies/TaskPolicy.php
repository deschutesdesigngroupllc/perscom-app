<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class TaskPolicy extends Policy
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
        return $this->hasPermissionTo($user, 'view:task') || $user->tokenCan('view:task');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'view:task') ||
               $user->tokenCan('view:task') ||
               $task->users->contains($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermissionTo($user, 'create:task') || $user->tokenCan('create:task');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'update:task') || $user->tokenCan('update:task');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user->tokenCan('delete:task');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user->tokenCan('delete:task');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user->tokenCan('delete:task');
    }

    /**
     * Determine where the user can attach to the model.
     */
    public function attachAnyUser(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    /**
     * Determine where the user can attach to the model.
     */
    public function attachUser(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    /**
     * Determine where the user can attach to the model.
     */
    public function detachUser(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
