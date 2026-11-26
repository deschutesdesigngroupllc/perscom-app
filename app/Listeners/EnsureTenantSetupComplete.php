<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Exceptions\TenantAccountSetupNotComplete;
use App\Models\Tenant;
use Illuminate\Support\Facades\App;
use Stancl\Tenancy\Events\TenancyInitialized;

class EnsureTenantSetupComplete
{
    /**
     * @throws TenantAccountSetupNotComplete when the tenant has not finished setup
     */
    public function handle(TenancyInitialized $event): void
    {
        /** @var Tenant $tenant */
        $tenant = $event->tenancy->tenant;

        if (App::runningInConsole()) {
            return;
        }

        if (App::runningConsoleCommand()) {
            return;
        }

        if (! $tenant->setup_completed) {
            throw new TenantAccountSetupNotComplete(
                403,
                'We are still working on setting up your account. We will email you when we are finished.',
            );
        }
    }
}
