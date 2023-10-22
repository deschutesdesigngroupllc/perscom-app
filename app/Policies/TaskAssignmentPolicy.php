<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Request;

class TaskAssignmentPolicy extends Policy
{
    public function before(): ?bool
    {
        if (Request::isCentralRequest()) {
            return false;
        }
    }

    public function viewAny(User $user = null): bool
    {
        return true;
    }

    public function view(User $user = null, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('view', $taskAssignment->task ?? new Task()) || $taskAssignment->user?->id === $user?->id;
    }

    public function create(User $user = null): bool
    {
        return Gate::check('create', Task::class);
    }

    public function update(User $user = null, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('update', $taskAssignment->task ?? new Task());
    }

    public function delete(User $user = null, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('delete', $taskAssignment->task ?? new Task());
    }

    public function restore(User $user = null, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('restore', $taskAssignment->task ?? new Task());
    }

    public function forceDelete(User $user = null, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('forceDelete', $taskAssignment->task ?? new Task());
    }
}
