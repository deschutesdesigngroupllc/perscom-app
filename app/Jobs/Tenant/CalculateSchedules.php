<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Contracts\RunsPerTenant;
use App\Jobs\Concerns\RunsForTenant;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class CalculateSchedules implements RunsPerTenant, ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;
    use RunsForTenant;

    public function __construct(public ?int $tenantKey = null)
    {
        $this->configureForTenancy();
    }

    public function work(): void
    {
        Schedule::all()->each(function (Schedule $schedule): void {
            $schedule->updateQuietly([
                'next_occurrence' => ScheduleService::nextOccurrence($schedule),
                'last_occurrence' => ScheduleService::lastOccurrence($schedule),
            ]);
        });
    }
}
