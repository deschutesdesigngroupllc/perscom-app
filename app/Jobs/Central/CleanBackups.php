<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;

class CleanBackups implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public function __construct()
    {
        $this->onQueue('backup');
        $this->onConnection('central');
    }

    public function handle(): void
    {
        $exit = Artisan::call('backup:clean');

        if ($exit !== 0) {
            $this->fail(Artisan::output());
        }
    }
}
