<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\Central;

use App\Jobs\Central\CleanBackups;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Central\CentralTestCase;

class CleanBackupsTest extends CentralTestCase
{
    public function test_it_targets_the_backup_queue_on_the_central_connection(): void
    {
        $job = new CleanBackups;

        $this->assertSame('backup', $job->queue);
        $this->assertSame('central', $job->connection);
    }

    public function test_it_runs_the_backup_clean_command(): void
    {
        $called = false;

        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:clean')
            ->andReturnUsing(function () use (&$called): int {
                $called = true;

                return 0;
            });

        (new CleanBackups)->handle();

        $this->assertTrue($called);
    }
}
