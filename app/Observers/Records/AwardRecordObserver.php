<?php

namespace App\Observers\Records;

use App\Models\Records\Award;
use App\Notifications\Records\NewAwardRecord;
use Illuminate\Support\Facades\Notification;

class AwardRecordObserver
{
    /**
     * Handle the Award "created" event.
     *
     * @param  \App\Models\Records\Award  $award
     * @return void
     */
    public function created(Award $award)
    {
        Notification::send($award->user, new NewAwardRecord($award));
    }

    /**
     * Handle the Award "updated" event.
     *
     * @param  \App\Models\Records\Award  $award
     * @return void
     */
    public function updated(Award $award)
    {
        //
    }

    /**
     * Handle the Award "deleted" event.
     *
     * @param  \App\Models\Records\Award  $award
     * @return void
     */
    public function deleted(Award $award)
    {
        //
    }

    /**
     * Handle the Award "restored" event.
     *
     * @param  \App\Models\Records\Award  $award
     * @return void
     */
    public function restored(Award $award)
    {
        //
    }

    /**
     * Handle the Award "force deleted" event.
     *
     * @param  \App\Models\Records\Award  $award
     * @return void
     */
    public function forceDeleted(Award $award)
    {
        //
    }
}
