<?php

namespace App\Listeners;

use App\Exceptions\TenantAccountSetupNotComplete;
use Stancl\Tenancy\Events\TenancyInitialized;

class CheckTenantDatabaseExists
{
    /**
     * @param  TenancyInitialized  $event
     * @return void
     */
    public function handle(TenancyInitialized $event)
    {
        $database = $event->tenancy->tenant->database()->getName();
        if (! $event->tenancy->tenant->database()->manager()->databaseExists($database)) {
            throw new TenantAccountSetupNotComplete(
                401, 'Sorry, we are still working on setting up your account. We will email you when we are finished.'
            );
        }
    }
}
