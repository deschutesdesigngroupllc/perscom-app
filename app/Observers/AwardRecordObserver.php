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
     * @return void
     */
    public function created(AwardRecord $award)
    {
        Notification::send($award->user, new NewAwardRecord($award));
    }

    /**
     * Handle the Award "updated" event.
     *
     * @return void
     */
    public function updated(AwardRecord $award)
    {
        //
    }

    /**
     * Handle the Award "deleted" event.
     *
     * @return void
     */
    public function deleted(AwardRecord $award)
    {
        //
    }

    /**
     * Handle the Award "restored" event.
     *
     * @return void
     */
    public function restored(AwardRecord $award)
    {
        //
    }

    /**
     * Handle the Award "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(AwardRecord $award)
    {
        //
    }
}
