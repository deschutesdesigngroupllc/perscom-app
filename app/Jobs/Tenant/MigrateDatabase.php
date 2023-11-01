<?php

namespace App\Jobs\Tenant;

use Illuminate\Support\Facades\Log;
use Throwable;

class MigrateDatabase extends \Stancl\Tenancy\Jobs\MigrateDatabase
{
    public function failed(Throwable $exception): void
    {
        Log::error('Failed to migrate tenant database', [
            'exception' => $exception,
        ]);
    }
}
