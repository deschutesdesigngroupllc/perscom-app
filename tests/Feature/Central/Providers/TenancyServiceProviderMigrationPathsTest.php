<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Providers;

use App\Providers\TenancyServiceProvider;
use Illuminate\Database\Migrations\Migrator;
use ReflectionMethod;
use Tests\Feature\Central\CentralTestCase;

class TenancyServiceProviderMigrationPathsTest extends CentralTestCase
{
    public function test_central_migrations_are_registered_when_tenancy_is_enabled(): void
    {
        $paths = resolve(Migrator::class)->paths();

        $this->assertContains(database_path('migrations/central'), $paths);
        $this->assertNotContains(database_path('migrations/tenant'), $paths);
    }

    public function test_tenant_and_settings_migrations_are_registered_when_tenancy_is_disabled(): void
    {
        config(['tenancy.enabled' => false]);

        $method = new ReflectionMethod(TenancyServiceProvider::class, 'registerMigrationPaths');
        $method->invoke(new TenancyServiceProvider($this->app));

        $paths = resolve(Migrator::class)->paths();

        $this->assertContains(database_path('migrations/tenant'), $paths);
        $this->assertContains(database_path('settings/tenant'), $paths);
    }
}
