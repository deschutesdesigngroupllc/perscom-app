<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Request;

class TaskPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:task') || $user?->tokenCan('view:task');
    }

    public function view(User $user = null, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'view:task') ||
               $user?->tokenCan('view:task') ||
               $task->users->contains($user);
    }

    public function create(User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:task') || $user?->tokenCan('create:task');
    }

    public function update(User $user = null, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'update:task') || $user?->tokenCan('update:task');
    }

    public function delete(User $user = null, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user?->tokenCan('delete:task');
    }

    public function restore(User $user = null, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user?->tokenCan('delete:task');
    }

    public function forceDelete(User $user = null, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || $user?->tokenCan('delete:task');
    }

    public function attachAnyUser(User $user = null, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function attachUser(User $user = null, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function detachUser(User $user = null, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
