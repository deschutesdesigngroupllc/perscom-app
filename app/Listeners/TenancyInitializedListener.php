<?php

namespace App\Listeners;

use App\Exceptions\TenantAccountSetupNotComplete;
use Illuminate\Support\Facades\Config;
use Outl1ne\NovaSettings\NovaSettings;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Events\TenancyInitialized;

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
            throw new TenantAccountSetupNotComplete(
                401, 'Sorry, we are still working on setting up your account. We will email you when we are finished.'
            );
        }

        PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.'.$event->tenancy->tenant->id;

        Config::set('app.timezone', NovaSettings::getSetting('timezone', \config('app.timezone')));
    }
}
