<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Models\ApiLog;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class PruneApiLogs implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public int $tenantKey, public int $days = 30)
    {
        $this->onQueue('central');
        $this->onConnection('central');
    }

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function (): void {
            $cutOffDate = Carbon::now()->subDays($this->days)->format('Y-m-d H:i:s');

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
        });
    }
}
