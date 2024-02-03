<?php

namespace App\Observers;

use App\Models\Domain;
use App\Notifications\System\DomainCreated;
use App\Notifications\System\DomainDeleted;
use App\Notifications\System\DomainUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DomainObserver
{
    public function created(Domain $domain): void
    {
        Log::debug('Create', [
            'tenant' => tenant(),
            'domain' => $domain,
            'backtrace' => debug_backtrace()
        ]);

        Notification::send($domain->tenant, new DomainCreated($domain));
    }

    public function updated(Domain $domain): void
    {
        Notification::send($domain->tenant, new DomainUpdated($domain));
    }

    public function deleted(Domain $domain): void
    {
        Log::debug('Deleted', [
            'tenant' => tenant(),
            'domain' => $domain,
            'backtrace' => debug_backtrace()
        ]);

        Notification::send($domain->tenant, new DomainDeleted($domain->url, $domain->tenant->url));
    }
}
