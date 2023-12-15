<?php

namespace App\Jobs;

use App\Models\Tenant;
use Database\Seeders\TenantSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResetDemoAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected string $seederClass)
    {
        $this->onQueue('system');
    }

    public function handle(): void
    {
        if ($tenant = Tenant::find(config('tenancy.demo_id'))) {
            Artisan::call('tenants:migrate-fresh', [
                '--tenants' => $tenant->getTenantKey(),
            ]);

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getTenantKey(),
                '--class' => TenantSeeder::class,
            ]);

            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->getTenantKey(),
                '--class' => $this->seederClass,
            ]);
        }
    }

    /**
     * @return int[]
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to reset demo account', [
            'exception' => $exception,
        ]);
    }
}
