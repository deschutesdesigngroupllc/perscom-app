<?php

namespace App\Jobs\Tenant;

use Illuminate\Support\Facades\Log;

class MigrateDatabase extends \Stancl\Tenancy\Jobs\MigrateDatabase
{
    public function failed(mixed $exception): void
    {
        Log::error('Failed to migrate tenant database', [
            'exception' => $exception,
        ]);
    }
}
