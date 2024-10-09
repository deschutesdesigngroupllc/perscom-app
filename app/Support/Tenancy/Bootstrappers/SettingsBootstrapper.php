<?php

declare(strict_types=1);

namespace App\Support\Tenancy\Bootstrappers;

use Illuminate\Support\Facades\App;
use Spatie\LaravelSettings\Support\SettingsCacheFactory;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class SettingsBootstrapper implements TenancyBootstrapper
{
    public function __construct(protected ?string $originalCachePrefix = null)
    {
        $this->originalCachePrefix = config('settings.cache.prefix');
    }

    public function bootstrap(Tenant $tenant): void
    {
        App::offsetUnset(SettingsCacheFactory::class);

        config()->set('settings.cache.prefix', "tenant_{$tenant->getTenantKey()}");

        app()->makeWith(SettingsCacheFactory::class, [
            'settingsConfig' => config('settings'),
        ]);
    }

    public function revert(): void
    {
        App::offsetUnset(SettingsCacheFactory::class);

        config()->set('settings.cache.prefix', $this->originalCachePrefix);

        app()->makeWith(SettingsCacheFactory::class, [
            'settingsConfig' => config('settings'),
        ]);
    }
}
