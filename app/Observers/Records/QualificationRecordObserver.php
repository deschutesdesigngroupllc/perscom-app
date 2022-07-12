<?php

namespace App\Observers\Records;

use App\Models\Records\Qualification;
use App\Notifications\Records\NewQualificationRecord;
use Illuminate\Support\Facades\Notification;

class QualificationRecordObserver
{
    /**
     * Handle the Qualification "created" event.
     *
     * @param  \App\Models\Records\Qualification  $qualification
     * @return void
     */
    public function created(Qualification $qualification)
    {
        Notification::send($qualification->person->users, new NewQualificationRecord($qualification));
    }

    /**
     * Handle the Qualification "updated" event.
     *
     * @param  \App\Models\Records\Qualification  $qualification
     * @return void
     */
    public function updated(Qualification $qualification)
    {
        //
    }

    /**
     * Handle the Qualification "deleted" event.
     *
     * @param  \App\Models\Records\Qualification  $qualification
     * @return void
     */
    public function deleted(Qualification $qualification)
    {
        //
    }

    /**
     * Handle the Qualification "restored" event.
     *
     * @param  \App\Models\Records\Qualification  $qualification
     * @return void
     */
    public function restored(Qualification $qualification)
    {
        //
    }

    /**
     * Handle the Qualification "force deleted" event.
     *
     * @param  \App\Models\Records\Qualification  $qualification
     * @return void
     */
    public function forceDeleted(Qualification $qualification)
    {
        //
    }
}
