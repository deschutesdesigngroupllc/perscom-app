<?php

declare(strict_types=1);

namespace App\Jobs\System;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

class ResetDemoAccount implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $seeder = 'military')
    {
        $this->onQueue('system');
    }

    public function handle(): void
    {
        Artisan::call('perscom:install', [
            '-n' => true,
            '--demo' => true,
            '--force' => true,
        ]);
    }

    /**
     * @return int[]
     */
    public function backoff(): array
    {
        return [1, 5, 10];
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Failed to reset demo account', [
            'exception' => $exception,
        ]);
    }
}
