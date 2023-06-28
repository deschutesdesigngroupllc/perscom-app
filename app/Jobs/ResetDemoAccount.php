<?php

namespace App\Jobs;

use App\Models\Tenant;
use Database\Seeders\Demo\Military\DemoDataSeeder;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ResetDemoAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('system');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($tenant = Tenant::find(config('tenancy.demo_id'))) {
            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $tenant->getTenantKey(),
            ]);

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getTenantKey(),
                '--class' => DemoDataSeeder::class,
            ]);
        }
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Failed to reset demo account', [
            'exception' => $exception,
        ]);
    }
}
