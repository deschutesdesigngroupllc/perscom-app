<?php

namespace App\Observers;

use App\Models\TaskAssignment;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentObserver
{
    /**
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the TaskAssignment "created" event.
     *
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return void
     */
    public function created(TaskAssignment $taskAssignment)
    {
        Notification::send($taskAssignment->user, new NewTaskAssignment($taskAssignment));
    }

    /**
     * Handle the TaskAssignment "updated" event.
     *
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return void
     */
    public function updated(TaskAssignment $taskAssignment)
    {
        //
    }

    /**
     * Handle the TaskAssignment "deleted" event.
     *
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return void
     */
    public function deleted(TaskAssignment $taskAssignment)
    {
        //
    }

    /**
     * Handle the TaskAssignment "restored" event.
     *
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return void
     */
    public function restored(TaskAssignment $taskAssignment)
    {
        //
    }

    /**
     * Handle the TaskAssignment "force deleted" event.
     *
     * @param  \App\Models\TaskAssignment  $taskAssignment
     * @return void
     */
    public function forceDeleted(TaskAssignment $taskAssignment)
    {
        //
    }
}
