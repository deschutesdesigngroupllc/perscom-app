<?php

declare(strict_types=1);

namespace App\Jobs\Central;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class BackupCentralDatabase implements ShouldQueue
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
        Config::set('backup.backup.source.databases', ['mysql']);

        $exit = Artisan::call('backup:run', [
            '--only-to-disk' => 'backups',
            '--only-db' => true,
            '--timeout' => 1800,
        ]);

        if ($exit !== 0) {
            $this->fail(Artisan::output());
        }
    }
}
