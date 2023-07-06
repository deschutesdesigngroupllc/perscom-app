<?php

namespace App\Jobs\Tenant;

use Illuminate\Support\Facades\Log;

class CreateDatabase extends \Stancl\Tenancy\Jobs\CreateDatabase
{
    /**
     * Handle a job failure.
     */
    public function failed($exception): void
    {
        Log::error('Failed to create tenant database', [
            'exception' => $exception,
        ]);
    }
}
