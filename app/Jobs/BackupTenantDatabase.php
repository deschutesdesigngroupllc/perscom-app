<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class BackupTenantDatabase implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public int $timeout = 300;

    public function __construct(public int $tenantKey)
    {
        $this->onQueue('backup');
    }

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function (Tenant $tenant) {
            Artisan::call('backup:run', [
                '--only-to-disk' => 's3',
                '--only-db' => true,
            ]);
        });
    }
}
