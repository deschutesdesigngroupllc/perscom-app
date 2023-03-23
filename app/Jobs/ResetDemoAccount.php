<?php

namespace App\Jobs;

use App\Models\Tenant;
use Database\Seeders\Demo\Military\DemoDataSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class ResetDemoAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
}
