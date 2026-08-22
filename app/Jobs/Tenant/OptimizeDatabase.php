<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Jobs\Concerns\RunsForTenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class OptimizeDatabase implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public int $tries = 1;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy(queue: 'clean');
    }

    public function work(): void
    {
        // OPTIMIZE TABLE / SHOW TABLES are MySQL-specific; skip on other drivers.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $tables = DB::select('SHOW TABLES');
        foreach ($tables as $table) {
            $table = array_values((array) $table)[0];
            DB::statement(sprintf('OPTIMIZE TABLE `%s`', $table));
        }
    }
}
