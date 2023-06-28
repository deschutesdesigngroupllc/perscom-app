<?php

namespace App\Jobs\Tenant;

use Exception;
use Illuminate\Support\Facades\Log;

class SeedDatabase extends \Stancl\Tenancy\Jobs\SeedDatabase
{
    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Failed to seed tenant database', [
            'exception' => $exception,
        ]);
    }
}
