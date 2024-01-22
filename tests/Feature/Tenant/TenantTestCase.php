<?php

namespace Tests\Feature\Tenant;

use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TestingCentralSeeder;
use Database\Seeders\TestingTenantSeeder;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\URL;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\TestCase;
use Tests\Traits\TenantHelpers;

class TenantTestCase extends TestCase
{
    use TenantHelpers;

    protected ?Admin $admin = null;

    protected ?Domain $domain = null;

    protected ?Tenant $tenant = null;

    protected ?User $user = null;

    public string $seeder = TestingCentralSeeder::class;

    public array $connectionsToTransact = ['mysql'];

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        putenv('TENANT_TESTING=true');

        parent::setUp();

        $this->tenant = Tenant::firstOrFail();
        $this->domain = Domain::firstOrFail();

        if (method_exists($this, 'beforeInitializingTenancy')) {
            $this->beforeInitializingTenancy($this->tenant);
        }

        tenancy()->initialize($this->tenant);
        tenant()->load('domains');

        URL::forceRootUrl($this->tenant->url);

        $this->user = User::firstOrFail();

        if (method_exists($this, 'afterInitializingTenancy')) {
            $this->afterInitializingTenancy($this->tenant);
        }
    }

    /**
     * @throws DatabaseManagerNotRegisteredException
     * @throws Exception
     */
    protected function afterRefreshingDatabase(): void
    {
        Log::debug('Admins', [
            'admin' => Admin::count(),
        ]);
        $testToken = ParallelTesting::token() ?: 1;

        $tenantName = "Tenant {$testToken}";
        $tenantDatabaseName = "tenant{$testToken}_testing";

        $tenant = Tenant::factory()->state([
            'name' => $tenantName,
            'tenancy_db_name' => $tenantDatabaseName,
        ])->createQuietly();

        Domain::factory()->state([
            'domain' => "tenant_{$tenant->getKey()}_$testToken",
            'tenant_id' => $tenant->getKey(),
        ])->createQuietly();

        if (! $tenant->database()->manager()->databaseExists($tenantDatabaseName)) {
            $tenant->database()->manager()->createDatabase($tenant);
        }

        $this->artisan('tenants:migrate-fresh', [
            '--tenants' => $tenant->getKey(),
        ]);
        $this->artisan('tenants:seed', [
            '--tenants' => $tenant->getKey(),
        ]);
        $this->artisan('tenants:seed', [
            '--tenants' => $tenant->getKey(),
            '--class' => TestingTenantSeeder::class,
        ]);
    }

    protected function tearDown(): void
    {
        $this->beforeApplicationDestroyed(function () {
            tenancy()->end();
        });

        parent::tearDown();

        putenv('TENANT_TESTING=false');
    }
}
