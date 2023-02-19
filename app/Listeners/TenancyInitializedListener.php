<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\TenancyInitialized;
use Stancl\Tenancy\Exceptions\TenantDatabaseDoesNotExistException;

class TenancyInitializedListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TenancyInitialized $event)
    {
        $database = $event->tenancy->tenant->database()->getName();
        if (! $event->tenancy->tenant->database()->manager()->databaseExists($database)) {
            throw new TenantDatabaseDoesNotExistException($database);
        }
    }
}
