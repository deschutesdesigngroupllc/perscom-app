<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class OptimizeDatabase implements ShouldQueue
{
    use Batchable;
    use InteractsWithQueue;
    use Queueable;

    public int $tries = 1;

    public function __construct(public int $tenantKey)
    {
        $this->onQueue('clean');
        $this->onConnection('central');
    }

    public function handle(): void
    {
        if ($this->batch()?->canceled()) {
            return;
        }

        Tenant::findOrFail($this->tenantKey)->run(function (): void {
            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $table) {
                $table = array_values((array) $table)[0];
                DB::statement(sprintf('OPTIMIZE TABLE `%s`', $table));
            }
        });
    }
}
