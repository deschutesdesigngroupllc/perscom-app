<?php

namespace App\Jobs\Tenant;

use Illuminate\Support\Facades\Log;
use Throwable;

class SeedDatabase extends \Stancl\Tenancy\Jobs\SeedDatabase
{
    public function failed(Throwable $exception): void
    {
        Log::error('Failed to seed tenant database', [
            'exception' => $exception,
        ]);
    }
}
