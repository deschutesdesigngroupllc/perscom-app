<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Exceptions\TenantAccountSetupNotComplete;
use Illuminate\Support\Facades\Cache;
use Stancl\Tenancy\Events\TenancyInitialized;
use Throwable;

class CheckTenantDatabaseExists
{
    /**
     * @throws Throwable
     */
    public function handle(TenancyInitialized $event): void
    {
        $tenant = $event->tenancy->tenant;

        if ($tenant && method_exists($tenant, 'database')) {
            $database = $tenant->database()->getName();

            $exists = Cache::remember("tenant_{$tenant->getKey()}_database_exists", 30, function () use ($tenant, $database) {
                return $tenant->database()->manager()->databaseExists($database);
            });

            throw_unless($exists, new TenantAccountSetupNotComplete(401, 'Sorry, we are still working on setting up your account. We will email you when we are finished.'));
        }
    }
}
