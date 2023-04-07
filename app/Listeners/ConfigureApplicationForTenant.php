<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Outl1ne\NovaSettings\NovaSettings;
use Spatie\Permission\PermissionRegistrar;

class ConfigureApplicationForTenant
{
    /**
     * @return void
     */
    public function handle()
    {
        optional(\tenant(), static function (Tenant $tenant) {
            $tenant->run(function ($tenant) {
                PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.'.$tenant->id;
                Config::set('app.timezone', NovaSettings::getSetting('timezone', \config('app.timezone')));
                Config::set('mail.from.name', $tenant->name);
            });
        });
    }
}
