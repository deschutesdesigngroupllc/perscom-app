<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class PurgeActivityLogs implements ShouldQueue
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
            $exit = Artisan::call('activitylog:clean', [
                '--days' => $this->days,
                '--force' => true,
            ]);

            if ($exit !== 0) {
                $this->fail(Artisan::output());
            }
        });
    }
}
