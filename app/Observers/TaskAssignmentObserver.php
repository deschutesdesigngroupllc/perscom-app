<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TaskAssignment;
use App\Notifications\Tenant\NewTaskAssignment;
use Illuminate\Support\Facades\Notification;

class TaskAssignmentObserver
{
    public function created(TaskAssignment $taskAssignment): void
    {
        Notification::send($taskAssignment->user, new NewTaskAssignment($taskAssignment));
    }
}
