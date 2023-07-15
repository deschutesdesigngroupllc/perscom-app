<?php

namespace App\Observers;

use App\Models\TaskAssignment;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentObserver
{
    /**
     * Handle the TaskAssignment "created" event.
     */
    public function created(TaskAssignment $taskAssignment): void
    {
        Notification::send($taskAssignment->user, new NewTaskAssignment($taskAssignment));
    }

    /**
     * Handle the TaskAssignment "updated" event.
     */
    public function updated(TaskAssignment $taskAssignment): void
    {
        //
    }

    /**
     * Handle the TaskAssignment "deleted" event.
     */
    public function deleted(TaskAssignment $taskAssignment): void
    {
        //
    }

    /**
     * Handle the TaskAssignment "restored" event.
     */
    public function restored(TaskAssignment $taskAssignment): void
    {
        //
    }

    /**
     * Handle the TaskAssignment "force deleted" event.
     */
    public function forceDeleted(TaskAssignment $taskAssignment): void
    {
        //
    }
}
