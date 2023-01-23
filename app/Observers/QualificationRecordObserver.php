<?php

namespace App\Observers;

use App\Models\QualificationRecord;
use App\Notifications\Tenant\NewQualificationRecord;
use Illuminate\Support\Facades\Notification;

class QualificationRecordObserver
{
    /**
     * Handle the Qualification "created" event.
     *
     * @param  \App\Models\QualificationRecord  $qualification
     * @return void
     */
    public function created(QualificationRecord $qualification)
    {
        Notification::send($qualification->user, new NewQualificationRecord($qualification));
    }

    /**
     * Handle the Qualification "updated" event.
     *
     * @param  \App\Models\QualificationRecord  $qualification
     * @return void
     */
    public function updated(QualificationRecord $qualification)
    {
        //
    }

    /**
     * Handle the Qualification "deleted" event.
     *
     * @param  \App\Models\QualificationRecord  $qualification
     * @return void
     */
    public function deleted(QualificationRecord $qualification)
    {
        //
    }

    /**
     * Handle the Qualification "restored" event.
     *
     * @param  \App\Models\QualificationRecord  $qualification
     * @return void
     */
    public function restored(QualificationRecord $qualification)
    {
        //
    }

    /**
     * Handle the Qualification "force deleted" event.
     *
     * @param  \App\Models\QualificationRecord  $qualification
     * @return void
     */
    public function forceDeleted(QualificationRecord $qualification)
    {
        //
    }
}
