<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Jobs\Concerns\RunsForTenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class BackupDatabase implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy(queue: 'backup');
    }

    public function work(): void
    {
        $exit = Artisan::call('backup:run', [
            '--only-to-disk' => 'backups',
            '--only-db' => true,
            '--timeout' => 1800,
        ]);

        if ($exit !== 0) {
            $this->fail(Artisan::output());
        }
    }
}
