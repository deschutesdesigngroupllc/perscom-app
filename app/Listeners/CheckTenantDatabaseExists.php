<?php

namespace App\Listeners;

use App\Exceptions\TenantAccountSetupNotComplete;
use Stancl\Tenancy\Events\TenancyInitialized;

class CheckTenantDatabaseExists
{
    public function handle(TenancyInitialized $event): void
    {
        if (method_exists($event->tenancy->tenant, 'database')) {
            $database = $event->tenancy->tenant->database()->getName();
            if (! $event->tenancy->tenant->database()->manager()->databaseExists($database)) {
                throw new TenantAccountSetupNotComplete(401, 'Sorry, we are still working on setting up your account. We will email you when we are finished.');
            }
        }
    }
}
