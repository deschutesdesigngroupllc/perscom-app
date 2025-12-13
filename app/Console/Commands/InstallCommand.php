<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\ApiKeySeeder;
use Database\Seeders\CentralDatabaseSeeder;
use Database\Seeders\FireServiceSeeder;
use Database\Seeders\MilitarySeeder;
use Database\Seeders\TenantDatabaseSeeder;
use Database\Seeders\TenantSeeder;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;

use function Laravel\Prompts\table;

class InstallCommand extends Command implements Isolatable
{
    protected $signature = 'perscom:install
                            {--seeder=military : The seeder to use. Default: military}';

    protected $description = 'Install the PERSCOM application.';

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

    protected function ensureInitialMigrationsRun(): void
    {
        try {
            if (! Schema::hasTable('migrations') || ! Schema::hasTable('tenants')) {
                $this->components->info('Initial migrations not found. Running initial migration setup...');
                $this->call('migrate', ['--step' => true]);
            }
        } catch (Exception) {
            $this->components->info('Database not properly initialized. Running initial migration setup...');
            $this->call('migrate', ['--step' => true]);
        }
    }

    protected function resetDemoEnvironment(): int
    {
        if (! app()->environment('demo', 'testing', 'local')) {
            $this->components->error('You may only run this command in the demo, testing or local environment.');

            return static::FAILURE;
        }

        $this->ensureInitialMigrationsRun();

        $seeder = match (true) {
            $this->option('seeder') === 'fire' => FireServiceSeeder::class,
            default => MilitarySeeder::class
        };

        /** @var Tenant|null $tenant */
        $tenant = Tenant::find(config('demo.tenant_id'));

        if (! $tenant) {
            $this->components->error('Please set a demo tenant ID in the demo config.');

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
            $this->components->error('You may only run this command in the local environment.');

            return static::FAILURE;
        }

        $this->ensureInitialMigrationsRun();

        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
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
            $this->components->error('We could not find a tenant to reset. Please try again.');

            return static::FAILURE;
        }

        if (filled($tenant->tenancy_db_name) && ! $tenant->database()->manager()->databaseExists($tenant->tenancy_db_name)) {
            $tenant->database()->manager()->createDatabase($tenant);
        }

        $this->call('tenants:migrate-fresh', [
            '--tenants' => $tenant->getTenantKey(),
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => TenantDatabaseSeeder::class,
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => ApiKeySeeder::class,
        ]);

        $this->components->info('Available tenants:');

        table(['ID', 'Tenant', 'URL'], Tenant::all()->map(fn (Tenant $tenant): array => [$tenant->getTenantKey(), $tenant->name, $tenant->url]));

        $this->components->info('Available user accounts:');

        table(['ID', 'Name', 'Email', 'Roles', 'Password'], Tenant::first()->run(fn () => User::all()->map(fn (User $user): array => [$user->id, $user->name, $user->email, $user->roles->map->name->implode(', '), '---'])));

        $this->components->info('Application URLs:');

        table(['URL', 'Purpose'], [
            [route('web.landing.home'), 'Main Landing Page'],
            [config('api.url').DIRECTORY_SEPARATOR.config('api.version'), 'API Base URL'],
            [route('filament.admin.pages.dashboard'), 'Administrative Dashboard'],
            [Tenant::first()->url, 'Tenant Dashboard'],
        ]);

        $this->components->success('PERSCOM has been successfully installed. Use the information above to get started.');

        return static::SUCCESS;
    }
}
