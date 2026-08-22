<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Jobs\Concerns\RunsForTenant;
use App\Models\ApiLog;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PruneApiLogs implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null, public int $days = 30)
    {
        $this->configureForTenancy(queue: 'clean');
    }

    public function work(): void
    {
        $cutOffDate = Date::now()->subDays($this->days)->format('Y-m-d H:i:s');

        $idsToDelete = ApiLog::query()
            ->where('created_at', '<', $cutOffDate)
            ->pluck('id');

        DB::query()
            ->from('meta')
            ->where('owner_type', ApiLog::class)
            ->whereIn('owner_id', $idsToDelete)
            ->delete();

        ApiLog::query()
            ->whereIn('id', $idsToDelete)
            ->delete();
    }
}
