<?php

namespace App\Listeners;

use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\PermissionRegistrar;

class ConfigureApplicationForTenant
{
    protected ?Tenant $tenant = null;

    public function handle(): void
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

    protected function configureTimezone(): void
    {
        $timezone = setting('timezone', \config('app.timezone'));

        Config::set('app.timezone', $timezone);
        date_default_timezone_set($timezone);
    }

    protected function configureMail(): void
    {
        Config::set('mail.from.name', $this->tenant?->name);
    }

    protected function configureCache(): void
    {
        PermissionRegistrar::$cacheKey = 'spatie.permission.cache.tenant.'.$this->tenant?->id;
    }
}
