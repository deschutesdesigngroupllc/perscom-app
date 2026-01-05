<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\ApiKeySeeder;
use Database\Seeders\CentralDatabaseSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoSeeder;
use Database\Seeders\FireServiceSeeder;
use Database\Seeders\MilitarySeeder;
use Database\Seeders\TenantDatabaseSeeder;
use Database\Seeders\TenantSeeder;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Stancl\Tenancy\Exceptions\DatabaseManagerNotRegisteredException;

use function Laravel\Prompts\table;

class InstallCommand extends Command implements Isolatable
{
    use ConfirmableTrait;

    protected $signature = 'perscom:install
                            {--seeder=military : The seeder to use. Default: military}
                            {--force : Force the operation to run when in production}';

    protected $description = 'Install the PERSCOM application.';

    /**
     * @throws DatabaseManagerNotRegisteredException
     */
    public function handle(): int
    {
        if (! $this->input->isInteractive()) {
            $this->components->info('Running in non-interactive mode.');
        }

        if ($this->isInstalled()) {
            if (! $this->option('force')) {
                $this->components->info('Application is already installed.');
                $this->components->info('Use --force to reinstall.');

                return self::SUCCESS;
            }

            if (! $this->confirmToProceed('Application is already installed. This will RESET all data!')) {
                return self::SUCCESS;
            }

            $this->components->warn('Resetting application...');
            $this->resetApplication();
        }

        return match (App::environment()) {
            'demo' => $this->reinstallDemo(),
            default => $this->reinstallApplication(),
        };
    }

    protected function reinstallDemo(): int
    {
        if (config('tenancy.enabled') === false) {
            $this->components->error('The demo environment is meant to be run in tenancy mode. Please enable it to continue.');

            return static::FAILURE;
        }

        /** @var Tenant|null $tenant */
        $tenant = Tenant::find(config('demo.tenant_id'));

        if (! $tenant) {
            $this->components->error('Please set a demo tenant ID in the demo config.');

            return static::FAILURE;
        }

        $this->call('tenants:migrate-fresh', [
            '--tenants' => $tenant->getTenantKey(),
        ]);

        $this->call('tenants:migrate', [
            '--tenants' => $tenant->getTenantKey(),
            '--path' => database_path('settings/tenant'),
            '--realpath' => true,
            '--schema-path' => database_path('settings/tenant'),
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => TenantSeeder::class,
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => DemoSeeder::class,
        ]);

        $seeder = match (true) {
            $this->option('seeder') === 'fire' => FireServiceSeeder::class,
            default => MilitarySeeder::class
        };

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => $seeder,
        ]);

        $this->components->success('The demo environment has been successfully reset.');

        return static::SUCCESS;
    }

    /**
     * @throws DatabaseManagerNotRegisteredException
     */
    protected function reinstallApplication(): int
    {
        return config('tenancy.enabled')
            ? $this->reinstallApplicationWithTenancy()
            : $this->reinstallApplicationWithoutTenancy();
    }

    /**
     * @throws DatabaseManagerNotRegisteredException
     */
    protected function reinstallApplicationWithTenancy(): int
    {
        tenancy()->runForMultiple(Tenant::all(), function (Tenant $tenant): void {
            if (filled($tenant->tenancy_db_name) && $tenant->database()->manager()->databaseExists($tenant->tenancy_db_name)) {
                $tenant->database()->manager()->deleteDatabase($tenant);
            }
        });

        $this->call('db:seed', [
            '--class' => CentralDatabaseSeeder::class,
            '--force' => true,
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

        $this->call('tenants:migrate', [
            '--tenants' => $tenant->getTenantKey(),
            '--force' => true,
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--force' => true,
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => TenantDatabaseSeeder::class,
            '--force' => true,
        ]);

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => ApiKeySeeder::class,
            '--force' => true,
        ]);

        $seeder = match (true) {
            $this->option('seeder') === 'fire' => FireServiceSeeder::class,
            default => MilitarySeeder::class
        };

        $this->call('tenants:seed', [
            '--tenants' => $tenant->getTenantKey(),
            '--class' => $seeder,
            '--force' => true,
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

    protected function reinstallApplicationWithoutTenancy(): int
    {
        $this->call('migrate', [
            '--path' => database_path('settings/tenant'),
            '--realpath' => true,
            '--schema-path' => database_path('settings/tenant'),
            '--force' => true,
        ]);

        $this->call('db:seed', [
            '--class' => DatabaseSeeder::class,
            '--force' => true,
        ]);

        $seeder = match (true) {
            $this->option('seeder') === 'fire' => FireServiceSeeder::class,
            default => MilitarySeeder::class
        };

        $this->call('db:seed', [
            '--class' => $seeder,
            '--force' => true,
        ]);

        $this->components->info('Available user accounts:');

        table(['ID', 'Name', 'Email', 'Roles', 'Password'], User::all()->map(fn (User $user): array => [$user->id, $user->name, $user->email, $user->roles->map->name->implode(', '), '---']));

        $this->components->info('Application URLs:');

        table(['URL', 'Purpose'], [
            [config('api.url').DIRECTORY_SEPARATOR.config('api.version'), 'API Base URL'],
            [route('filament.app.pages.dashboard'), 'App Dashboard'],
        ]);

        $this->components->success('PERSCOM has been successfully installed. Use the information above to get started.');

        return static::SUCCESS;
    }

    protected function isInstalled(): bool
    {
        return count(Schema::getTables()) > 0;
    }

    protected function resetApplication(): void
    {
        $this->components->info('Truncating all data...');

        if (config('tenancy.enabled')) {
            $this->call('migrate:fresh', [
                '--force' => true,
            ]);
        } else {
            $this->call('migrate:fresh', [
                '--path' => database_path('migrations/tenant'),
                '--realpath' => true,
                '--schema-path' => database_path('migrations/tenant'),
                '--force' => true,
            ]);
        }

        $this->components->success('Application reset complete.');
    }
}
