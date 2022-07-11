<?php

namespace App\Observers\Records;

use App\Models\Records\Rank;
use App\Notifications\Records\NewRankRecord;
use Illuminate\Support\Facades\Notification;

class RankRecordObserver
{
    /**
     * Handle the Rank "created" event.
     *
     * @param  \App\Models\Records\Rank  $rank
     * @return void
     */
    public function created(Rank $rank)
    {
	    Notification::send($rank->person->users, new NewRankRecord($rank));
    }

    /**
     * Handle the Rank "updated" event.
     *
     * @param  \App\Models\Records\Rank  $rank
     * @return void
     */
    public function updated(Rank $rank)
    {
        //
    }

    /**
     * Handle the Rank "deleted" event.
     *
     * @param  \App\Models\Records\Rank  $rank
     * @return void
     */
    public function deleted(Rank $rank)
    {
        //
    }

    /**
     * Handle the Rank "restored" event.
     *
     * @param  \App\Models\Records\Rank  $rank
     * @return void
     */
    public function restored(Rank $rank)
    {
        //
    }

    /**
     * Handle the Rank "force deleted" event.
     *
     * @param  \App\Models\Records\Rank  $rank
     * @return void
     */
    public function forceDeleted(Rank $rank)
    {
        //
    }
}
