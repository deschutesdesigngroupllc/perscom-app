<?php

namespace App\Bootstrappers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\PermissionRegistrar;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class ConfigBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant): void
    {
        App::forgetInstance('mail.manager');

        Config::set('mail.from.name', $tenant->getAttribute('name'));
        Config::set('app.timezone', setting('timezone', \config('app.timezone')));
        PermissionRegistrar::$cacheKey = "'spatie.permission.cache.tenant.{$tenant->getTenantKey()}";
    }

    public function revert(): void
    {
        App::forgetInstance('mail.manager');

        Config::set('mail.from.name', env('MAIL_FROM_NAME', 'TEST'));
        Config::set('app.timezone', 'UTC');
        PermissionRegistrar::$cacheKey = 'spatie.permission.cache';
    }
}
