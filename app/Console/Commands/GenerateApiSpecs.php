<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateApiSpecs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perscom:specs
                            {--format=yaml}
                            {--tenant=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the PERSCOM API specification.';

    /**
     * Execute the console command.
     */
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

        return self::SUCCESS;
    }
}
