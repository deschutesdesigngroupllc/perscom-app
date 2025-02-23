<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class CleanTenantBackups implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public int $tenantKey)
    {
        $this->onQueue('backup');
        $this->onConnection('central');
    }

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        /** @var Tenant $tenant */
        $tenant = Tenant::findOrFail($this->tenantKey);
        $tenant->run(function () {
            $exit = Artisan::call('backup:clean');

            if ($exit !== 0) {
                $this->fail(Artisan::output());
            }
        });
    }
}
