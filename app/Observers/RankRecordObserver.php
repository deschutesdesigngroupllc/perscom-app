<?php

namespace App\Observers;

use App\Models\RankRecord;
use App\Notifications\Tenant\NewRankRecord;
use Illuminate\Support\Facades\Notification;

class RankRecordObserver
{
    /**
     * Handle the Rank "created" event.
     *
     * @param  \App\Models\RankRecord  $rank
     * @return void
     */
    public function created(RankRecord $rank)
    {
        Notification::send($rank->user, new NewRankRecord($rank));
    }

    /**
     * Handle the Rank "updated" event.
     *
     * @param  \App\Models\RankRecord  $rank
     * @return void
     */
    public function updated(RankRecord $rank)
    {
        //
    }

    /**
     * Handle the Rank "deleted" event.
     *
     * @param  \App\Models\RankRecord  $rank
     * @return void
     */
    public function deleted(RankRecord $rank)
    {
        //
    }

    /**
     * Handle the Rank "restored" event.
     *
     * @param  \App\Models\RankRecord  $rank
     * @return void
     */
    public function restored(RankRecord $rank)
    {
        //
    }

    /**
     * Handle the Rank "force deleted" event.
     *
     * @param  \App\Models\RankRecord  $rank
     * @return void
     */
    public function forceDeleted(RankRecord $rank)
    {
        //
    }
}
