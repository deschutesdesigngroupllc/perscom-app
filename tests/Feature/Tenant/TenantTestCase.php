<?php

namespace Tests\Feature\Tenant;

use App\Models\Admin;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TestingCentralSeeder;
use Database\Seeders\TestingTenantSeeder;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactionsManager;
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

        $this->setupTenantTransactions();
    }

    /**
     * @throws DatabaseManagerNotRegisteredException
     * @throws Exception
     */
    protected function afterRefreshingDatabase(): void
    {
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

    public function setupTenantTransactions()
    {
        $database = $this->app->make('db');

        $this->app->instance('db.transactions', $transactionsManager = new DatabaseTransactionsManager);

        $connection = $database->connection('tenant');
        $connection->setTransactionManager($transactionsManager);
        $dispatcher = $connection->getEventDispatcher();

        $connection->unsetEventDispatcher();
        $connection->beginTransaction();
        $connection->setEventDispatcher($dispatcher);

        $this->beforeApplicationDestroyed(function () use ($database) {
            $connection = $database->connection('tenant');
            $dispatcher = $connection->getEventDispatcher();

            $connection->unsetEventDispatcher();
            $connection->rollBack();
            $connection->setEventDispatcher($dispatcher);
            $connection->disconnect();
        });
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
