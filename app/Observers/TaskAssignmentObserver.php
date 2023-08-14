<?php

namespace App\Observers;

use App\Models\TaskAssignment;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentObserver
{
    /**
     * Handle the TaskAssignment "created" event.
     *
     * @return void
     */
    public function created(TaskAssignment $taskAssignment)
    {
        Notification::send($taskAssignment->user, new NewTaskAssignment($taskAssignment));
    }
}
