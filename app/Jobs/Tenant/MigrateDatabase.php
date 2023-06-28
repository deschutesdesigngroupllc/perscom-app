<?php

namespace App\Jobs\Tenant;

use Exception;
use Illuminate\Support\Facades\Log;

class MigrateDatabase extends \Stancl\Tenancy\Jobs\MigrateDatabase
{
    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Failed to migrate tenant database', [
            'exception' => $exception,
        ]);
    }
}
