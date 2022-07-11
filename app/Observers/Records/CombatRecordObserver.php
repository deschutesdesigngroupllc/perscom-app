<?php

namespace App\Observers\Records;

use App\Models\Records\Combat;
use App\Notifications\Records\NewCombatRecord;
use Illuminate\Support\Facades\Notification;

class CombatRecordObserver
{
    /**
     * Handle the Combat "created" event.
     *
     * @param  \App\Models\Records\Combat  $combat
     * @return void
     */
    public function created(Combat $combat)
    {
	    Notification::send($combat->person->users, new NewCombatRecord($combat));
    }

    /**
     * Handle the Combat "updated" event.
     *
     * @param  \App\Models\Records\Combat  $combat
     * @return void
     */
    public function updated(Combat $combat)
    {
        //
    }

    /**
     * Handle the Combat "deleted" event.
     *
     * @param  \App\Models\Records\Combat  $combat
     * @return void
     */
    public function deleted(Combat $combat)
    {
        //
    }

    /**
     * Handle the Combat "restored" event.
     *
     * @param  \App\Models\Records\Combat  $combat
     * @return void
     */
    public function restored(Combat $combat)
    {
        //
    }

    /**
     * Handle the Combat "force deleted" event.
     *
     * @param  \App\Models\Records\Combat  $combat
     * @return void
     */
    public function forceDeleted(Combat $combat)
    {
        //
    }
}
