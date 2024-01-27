<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Database\Seeders\CentralDatabaseSeeder;
use Database\Seeders\TenantDatabaseSeeder;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ResetLocalEnvironment extends Command
{
    protected $signature = 'perscom:local';

    protected $description = 'Reset the local development environment.';

    /**
     * @throws DatabaseManagerNotRegisteredException
     */
    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('You may only run this command in the local environment.');

            return CommandAlias::FAILURE;
        }

        $this->call('migrate:fresh');
        $this->call('db:seed', [
            '--class' => CentralDatabaseSeeder::class,
        ]);

        $tenant = Tenant::firstOrFail();

        if (! $tenant->database()->manager()->databaseExists($tenant->tenancy_db_name)) {
            $tenant->database()->manager()->createDatabase($tenant);
        }

        $this->call('tenants:migrate-fresh');
        $this->call('tenants:seed');
        $this->call('tenants:seed', [
            '--class' => TenantDatabaseSeeder::class,
        ]);

        return CommandAlias::SUCCESS;
    }
}
