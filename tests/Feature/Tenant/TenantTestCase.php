<?php

namespace Tests\Feature\Tenant;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TestingCentralSeeder;
use Database\Seeders\TestingTenantSeeder;
use Exception;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Str;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Tests\TestCase;
use Tests\Traits\WithTenant;

class TenantTestCase extends TestCase
{
    use WithTenant;

    public string $seeder = TestingCentralSeeder::class;

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function setUp(): void
    {
        putenv('TENANT_TESTING=true');

        parent::setUp();

        if (method_exists($this, 'beforeSetUpTenancy')) {
            $this->beforeSetUpTenancy();
        }

        $this->tenant = Tenant::firstOrFail();

        tenancy()->initialize($this->tenant);
        tenant()->load('domains');

        $this->user = User::firstOrFail();

        if (method_exists($this, 'afterSetUpTenancy')) {
            $this->afterSetUpTenancy();
        }
    }

    /**
     * @throws DatabaseManagerNotRegisteredException
     * @throws Exception
     */
    protected function afterRefreshingDatabase(): void
    {
        $testToken = ParallelTesting::token() ?: Str::random();

        $tenantName = "Tenant {$testToken}";
        $tenantDatabaseName = "tenant{$testToken}_testing";

        $tenant = Tenant::factory()->state([
            'name' => $tenantName,
            'tenancy_db_name' => $tenantDatabaseName
        ])->createQuietly();

        if (! $tenant->database()->manager()->databaseExists($tenantDatabaseName)) {
            $tenant->database()->manager()->createDatabase($tenant);
        }

        $this->artisan('tenants:migrate-fresh');
        $this->artisan('tenants:seed');
        $this->artisan('tenants:seed', [
            '--class' => TestingTenantSeeder::class
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        putenv('TENANT_TESTING=false');
    }
}
