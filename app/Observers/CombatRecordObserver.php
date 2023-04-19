<?php

namespace App\Observers;

use App\Models\CombatRecord;
use App\Notifications\Tenant\NewCombatRecord;
use Illuminate\Support\Facades\Notification;

class CombatRecordObserver
{
    /**
     * Handle the Combat "created" event.
     *
     * @return void
     */
    public function created(CombatRecord $combat)
    {
        Notification::send($combat->user, new NewCombatRecord($combat));
    }

    /**
     * Handle the Combat "updated" event.
     *
     * @return void
     */
    public function updated(CombatRecord $combat)
    {
        //
    }

    /**
     * Handle the Combat "deleted" event.
     *
     * @return void
     */
    public function deleted(CombatRecord $combat)
    {
        //
    }

    /**
     * Handle the Combat "restored" event.
     *
     * @return void
     */
    public function restored(CombatRecord $combat)
    {
        //
    }

    /**
     * Handle the Combat "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(CombatRecord $combat)
    {
        //
    }
}
