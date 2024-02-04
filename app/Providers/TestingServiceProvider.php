<?php

namespace App\Providers;

use App\Models\Domain;
use App\Models\Tenant;
use Database\Seeders\TestingTenantSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Tests\TestCase;

class TestingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ParallelTesting::setUpProcess(function (int $token) {
            Log::debug("Set up process $token");
        });

        ParallelTesting::setUpTestCase(function (int $token, TestCase $testCase) {
            Log::debug("Set up test case $token", [
                'case' => $testCase
            ]);

            $tenantName = "Tenant {$token}";
            $tenantDatabaseName = "tenant{$token}_testing";

            $tenant = Tenant::factory()->state([
                'name' => $tenantName,
                'tenancy_db_name' => $tenantDatabaseName,
            ])->createQuietly();

            Domain::factory()->state([
                'domain' => "tenant_{$tenant->getKey()}_$token",
                'tenant_id' => $tenant->getKey(),
            ])->createQuietly();

            if (! $tenant->database()->manager()->databaseExists($tenantDatabaseName)) {
                $tenant->database()->manager()->createDatabase($tenant);
            }

            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $tenant->getKey(),
            ]);

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getKey(),
            ]);

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getKey(),
                '--class' => TestingTenantSeeder::class,
            ]);

            tenancy()->initialize($tenant);
            tenant()->load('domains');

            URL::forceRootUrl($tenant->url);
        });

        ParallelTesting::setUpTestDatabase(function (string $database, int $token) {
            Log::debug('Set up test database', [
                'database' => $database
            ]);
        });

        ParallelTesting::tearDownTestCase(function (int $token, TestCase $testCase) {
            Log::debug("Tear down test case $token", [
                'case' => $testCase
            ]);

            optional(\tenant(), function (Tenant $tenant) use ($token) {
                tenancy()->end();

                if ($tenant->database()->manager()->databaseExists("tenant{$token}_testing")) {
                    $tenant->database()->manager()->deleteDatabase($tenant);
                }
            });
        });

        ParallelTesting::tearDownProcess(function (int $token) {
            Log::debug("Tear down test process $token");
        });
    }
}
