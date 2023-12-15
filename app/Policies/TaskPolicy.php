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

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'view:task') || optional($user)->tokenCan('view:task');
    }

    public function view(?User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'view:task') ||
               optional($user)->tokenCan('view:task') ||
               $task->users->contains($user);
    }

    public function create(?User $user = null): bool
    {
        return $this->hasPermissionTo($user, 'create:task') || optional($user)->tokenCan('create:task');
    }

    public function update(?User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'update:task') || optional($user)->tokenCan('update:task');
    }

    public function delete(?User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || optional($user)->tokenCan('delete:task');
    }

    public function restore(?User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || optional($user)->tokenCan('delete:task');
    }

    public function forceDelete(?User $user, Task $task): bool
    {
        return $this->hasPermissionTo($user, 'delete:task') || optional($user)->tokenCan('delete:task');
    }

    public function attachAnyUser(?User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function attachUser(?User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function detachUser(?User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }
}
