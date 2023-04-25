<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\PermissionRegistrar;

class ConfigureApplicationForTenant
{
    protected Tenant|null $tenant = null;

    /**
     * @return void
     */
    public function handle()
    {
        optional(\tenant(), function (Tenant $tenant) {
            $this->tenant = $tenant;

            $this->tenant->run(function () {
                $this->configureTimezone();
                $this->configureMail();
                $this->configureCache();
            });
        });
    }

    /**
     * @return void
     */
    protected function configureTimezone()
    {
        $timezone = setting('timezone', \config('app.timezone'));

        Config::set('app.timezone', $timezone);
        date_default_timezone_set($timezone);
    }

    /**
     * @return void
     */
    protected function configureMail()
    {
        Config::set('mail.from.name', $this->tenant?->name);
    }

    /**
     * @return void
     */
    protected function configureCache()
    {
        PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.'.$this->tenant?->id;
    }
}
