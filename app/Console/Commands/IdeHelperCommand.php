<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use SplFileInfo;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Throwable;

#[Description('Generate IDE helper files, initializing tenancy only when it is enabled.')]
#[Signature('perscom:ide-helper {--models : Only regenerate the model docblocks}')]
class IdeHelperCommand extends Command
{
    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        return config('tenancy.enabled')
            ? $this->generateWithTenancy()
            : $this->generate();
    }

    /**
     * Generate the helpers while tenancy is initialized for the first tenant.
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    protected function generateWithTenancy(): int
    {
        $tenant = Tenant::query()->first();

        if (! $tenant instanceof Tenant) {
            $this->components->error('No tenant found to generate IDE helpers against.');

            return static::FAILURE;
        }

        tenancy()->initialize($tenant);

        try {
            return $this->generate();
        } finally {
            tenancy()->end();
        }
    }

    protected function generate(): int
    {
        if (! $this->option('models')) {
            $this->call('ide-helper:generate');
            $this->call('ide-helper:meta');
        }

        $options = [
            '--reset' => true,
            '--write' => true,
        ];

        if (filled($ignored = $this->modelsWithoutTables())) {
            $options['--ignore'] = collect($ignored)->implode(',');
        }

        $this->call('ide-helper:models', $options);

        return static::SUCCESS;
    }

    /**
     * Models whose backing table is absent on their connection, e.g. central/system
     * models (tenants, subscriptions, domains) when running outside tenancy. They are
     * skipped so their docblocks are not wiped when the table cannot be inspected.
     *
     * @return list<class-string<Model>>
     */
    protected function modelsWithoutTables(): array
    {
        return collect(File::files(app_path('Models')))
            ->map(fn (SplFileInfo $file): string => 'App\\Models\\'.$file->getBasename('.php'))
            ->filter(fn (string $model): bool => class_exists($model) && is_subclass_of($model, Model::class) && ! new ReflectionClass($model)->isAbstract())
            ->filter(function (string $model): bool {
                try {
                    $instance = new $model;

                    return ! Schema::connection($instance->getConnectionName())->hasTable($instance->getTable());
                } catch (Throwable) {
                    return true;
                }
            })
            ->values()
            ->all();
    }
}
