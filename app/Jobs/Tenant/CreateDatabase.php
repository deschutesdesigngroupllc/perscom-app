<?php

namespace App\Jobs\Tenant;

use Illuminate\Support\Facades\Log;
use Throwable;

class CreateDatabase extends \Stancl\Tenancy\Jobs\CreateDatabase
{
    public function failed(Throwable $exception): void
    {
        Log::error('Failed to create tenant database', [
            'exception' => $exception,
        ]);
    }
}
