<?php

namespace App\Observers;

use App\Models\TaskAssignment;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentObserver
{
    public function created(TaskAssignment $taskAssignment): void
    {
        if (is_null($taskAssignment->assigned_at)) {
            $taskAssignment->forceFill([
                'assigned_at' => now(),
            ])->save();
        }

        Notification::send($taskAssignment->user, new NewTaskAssignment($taskAssignment));
    }
}
