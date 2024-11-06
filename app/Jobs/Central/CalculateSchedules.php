<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use App\Models\Schedule;
use App\Models\Tenant;
use App\Services\ScheduleService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class CalculateSchedules implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(public int $tenantKey)
    {
        $this->onConnection('central');
    }

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function () {
            Schedule::all()->each(function (Schedule $schedule) {
                $schedule->updateQuietly([
                    'next_occurrence' => ScheduleService::nextOccurrence($schedule),
                    'last_occurrence' => ScheduleService::lastOccurrence($schedule),
                ]);
            });
        });
    }
}
