<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;

class TaskAssignmentPolicy extends Policy
{
    public function before(): ?bool
    {
        if (App::isAdmin()) {
            return false;
        }

        return null;
    }

    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('view', $taskAssignment->task ?? new Task) || optional($taskAssignment->user)->id === optional($user)->id;
    }

    public function create(?User $user = null): bool
    {
        return Gate::check('create', Task::class);
    }

    public function update(?User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('update', $taskAssignment->task ?? new Task);
    }

    public function delete(?User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('delete', $taskAssignment->task ?? new Task);
    }

    public function restore(?User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('restore', $taskAssignment->task ?? new Task);
    }

    public function forceDelete(?User $user, TaskAssignment $taskAssignment): bool
    {
        return Gate::check('forceDelete', $taskAssignment->task ?? new Task);
    }
}
