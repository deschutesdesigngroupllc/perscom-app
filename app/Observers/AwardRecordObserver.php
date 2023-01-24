<?php

namespace App\Observers;

use App\Models\AwardRecord;
use App\Notifications\Tenant\NewAwardRecord;
use Illuminate\Support\Facades\Notification;

class AwardRecordObserver
{
    /**
     * Handle the Award "created" event.
     *
     * @param  \App\Models\AwardRecord  $award
     * @return void
     */
    public function created(AwardRecord $award)
    {
        Notification::send($award->user, new NewAwardRecord($award));
    }

    /**
     * Handle the Award "updated" event.
     *
     * @param  \App\Models\AwardRecord  $award
     * @return void
     */
    public function updated(AwardRecord $award)
    {
        //
    }

    /**
     * Handle the Award "deleted" event.
     *
     * @param  \App\Models\AwardRecord  $award
     * @return void
     */
    public function deleted(AwardRecord $award)
    {
        //
    }

    /**
     * Handle the Award "restored" event.
     *
     * @param  \App\Models\AwardRecord  $award
     * @return void
     */
    public function restored(AwardRecord $award)
    {
        //
    }

    /**
     * Handle the Award "force deleted" event.
     *
     * @param  \App\Models\AwardRecord  $award
     * @return void
     */
    public function forceDeleted(AwardRecord $award)
    {
        //
    }
}
