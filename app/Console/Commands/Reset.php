<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Database\Seeders\CentralDatabaseSeeder;
use Database\Seeders\FireServiceSeeder;
use Database\Seeders\MilitarySeeder;
use Database\Seeders\TenantDatabaseSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;

class Reset extends Command implements Isolatable
{
    protected $signature = 'perscom:reset 
                            {--seeder=military : The seeder to use. Default: military}';

    protected $description = 'Reset\'s the perscom application.';

    /**
     * @throws DatabaseManagerNotRegisteredException
     */
    public function handle(): int
    {
        return match ($this->option('env')) {
            'demo' => $this->resetDemoEnvironment(),
            default => $this->resetLocalEnvironment(),
        };
    }

    protected function resetDemoEnvironment(): int
    {
        if (! app()->environment('demo', 'testing', 'local')) {
            $this->error('You may only run this command in the demo, testing or local environment.');

            return static::FAILURE;
        }

        $seeder = match (true) {
            $this->option('seeder') === 'fire' => FireServiceSeeder::class,
            default => MilitarySeeder::class
        };

        /** @var Tenant|null $tenant */
        $tenant = Tenant::find(config('demo.tenant_id'));

        if (! $tenant) {
            $this->error('Please set a demo tenant ID in the demo config.');

            return static::FAILURE;
        }

        $this->call('tenants:migrate-fresh', [
            '--tenants' => $tenant->getTenantKey(),
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => TenantSeeder::class,
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => $seeder,
        ]);

        return static::SUCCESS;
    }

    /**
     * @throws DatabaseManagerNotRegisteredException
     */
    protected function resetLocalEnvironment(): int
    {
        if (! app()->environment('local')) {
            $this->error('You may only run this command in the local environment.');

            return static::FAILURE;
        }

        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant) {
            if (filled($tenant->tenancy_db_name) && $tenant->database()->manager()->databaseExists($tenant->tenancy_db_name)) {
                $tenant->database()->manager()->deleteDatabase($tenant);
            }
        });

        $this->call('migrate:fresh');
        $this->call('db:seed', [
            '--class' => CentralDatabaseSeeder::class,
        ]);

        /** @var Tenant|null $tenant */
        $tenant = Tenant::first();

        if (! $tenant) {
            $this->error('We could not find a tenant to reset. Please try again.');

            return static::FAILURE;
        }

        if (filled($tenant->tenancy_db_name) && ! $tenant->database()->manager()->databaseExists($tenant->tenancy_db_name)) {
            $tenant->database()->manager()->createDatabase($tenant);
        }

        $this->call('tenants:migrate-fresh', [
            '--tenants' => [$tenant->getKey()],
        ]);
        $this->call('tenants:seed', [
            '--tenants' => [$tenant->getKey()],
        ]);
        $this->call('tenants:seed', [
            '--tenants' => [$tenant->getKey()],
            '--class' => TenantDatabaseSeeder::class,
        ]);

        return static::SUCCESS;
    }
}
