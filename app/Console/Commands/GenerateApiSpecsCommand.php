<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateApiSpecsCommand extends Command
{
    protected $signature = 'perscom:specs
                            {--format=yaml}
                            {--tenant=}';

    protected $description = 'Generates the PERSCOM API specification.';

    public function handle(): int
    {
        $tenant = optional($this->option('tenant'), function ($tenantId) {
            return Tenant::find($tenantId);
        });

        $tenant = $tenant ?? Tenant::first();

        $tenant->run(function () {
            Artisan::call('orion:specs', [
                '--format' => $this->option('format'),
            ]);
            $this->info(Artisan::output());
        });

        return static::SUCCESS;
    }
}
