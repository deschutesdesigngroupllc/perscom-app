<?php

namespace App\Observers\Records;

use App\Models\Records\Assignment;
use App\Notifications\Records\NewAssignmentRecord;
use App\Notifications\Records\NewAwardRecord;
use Illuminate\Support\Facades\Notification;

class AssignmentRecordObserver
{
    /**
     * Handle the Assignment "created" event.
     *
     * @param  \App\Models\Records\Assignment  $assignment
     * @return void
     */
    public function created(Assignment $assignment)
    {
        Notification::send($assignment->person->users, new NewAssignmentRecord($assignment));
    }

    /**
     * Handle the Assignment "updated" event.
     *
     * @param  \App\Models\Records\Assignment  $assignment
     * @return void
     */
    public function updated(Assignment $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "deleted" event.
     *
     * @param  \App\Models\Records\Assignment  $assignment
     * @return void
     */
    public function deleted(Assignment $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "restored" event.
     *
     * @param  \App\Models\Records\Assignment  $assignment
     * @return void
     */
    public function restored(Assignment $assignment)
    {
        //
    }

    /**
     * Handle the Assignment "force deleted" event.
     *
     * @param  \App\Models\Records\Assignment  $assignment
     * @return void
     */
    public function forceDeleted(Assignment $assignment)
    {
        //
    }
}
