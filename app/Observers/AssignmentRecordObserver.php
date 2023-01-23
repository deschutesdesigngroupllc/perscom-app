<?php

namespace App\Observers;

use App\Models\AssignmentRecord;
use App\Notifications\Tenant\NewAssignmentRecord;
use Illuminate\Support\Facades\Notification;

class AssignmentRecordObserver
{
    /**
     * Handle the Assignment "created" event.
     *
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return void
     */
    public function created(AssignmentRecord $assignment)
    {
        Notification::send($assignment->user, new NewAssignmentRecord($assignment));
    }

    /**
     * Handle the Assignment "updated" event.
     *
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return void
     */
    public function updated(AssignmentRecord $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "deleted" event.
     *
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return void
     */
    public function deleted(AssignmentRecord $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "restored" event.
     *
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return void
     */
    public function restored(AssignmentRecord $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "force deleted" event.
     *
     * @param  \App\Models\AssignmentRecord  $assignment
     * @return void
     */
    public function forceDeleted(AssignmentRecord $assignment)
    {
        //
    }
}
