<?php

namespace App\Bootstrappers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class ConfigBootstrapper implements TenancyBootstrapper
{
    protected ?string $mailFromName = null;

    protected ?string $timezone = null;

    public function bootstrap(Tenant $tenant): void
    {
        $this->mailFromName = config('mail.from.name');
        $this->timezone = config('app.timezone');

        App::forgetInstance('mail.manager');

        Config::set('mail.from.name', $tenant->getAttribute('name'));
        Config::set('app.timezone', setting('timezone', config('app.timezone')));
        PermissionRegistrar::$cacheKey = "'spatie.permission.cache.tenant.{$tenant->getTenantKey()}";
    }

    public function revert(): void
    {
        App::forgetInstance('mail.manager');

        Config::set('mail.from.name', $this->mailFromName);
        Config::set('app.timezone', $this->timezone);
        PermissionRegistrar::$cacheKey = 'spatie.permission.cache';
    }
}
