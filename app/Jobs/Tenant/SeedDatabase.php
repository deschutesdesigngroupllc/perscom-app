<?php

namespace App\Jobs\Tenant;

use Illuminate\Support\Facades\Log;

class SeedDatabase extends \Stancl\Tenancy\Jobs\SeedDatabase
{
    /**
     * Handle a job failure.
     */
    public function failed($exception): void
    {
        Log::error('Failed to seed tenant database', [
            'exception' => $exception,
        ]);
    }
}
