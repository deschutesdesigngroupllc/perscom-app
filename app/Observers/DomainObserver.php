<?php

namespace App\Observers;

use App\Models\Domain;
use App\Notifications\System\DomainCreated;
use App\Notifications\System\DomainDeleted;
use App\Notifications\System\DomainUpdated;
use Illuminate\Support\Facades\Notification;

class DomainObserver
{
    /**
     * Handle the Domain "created" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function created(Domain $domain)
    {
        Notification::send($domain->tenant, new DomainCreated($domain));
    }

    /**
     * Handle the Domain "updated" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function updated(Domain $domain)
    {
        Notification::send($domain->tenant, new DomainUpdated($domain));
    }

    /**
     * Handle the Domain "deleted" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function deleted(Domain $domain)
    {
        Notification::send($domain->tenant, new DomainDeleted($domain->url, $domain->tenant->url));
    }

    /**
     * Handle the Domain "restored" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function restored(Domain $domain)
    {
        //
    }

    /**
     * Handle the Domain "force deleted" event.
     *
     * @param  \App\Models\Domain  $domain
     * @return void
     */
    public function forceDeleted(Domain $domain)
    {
        //
    }
}
