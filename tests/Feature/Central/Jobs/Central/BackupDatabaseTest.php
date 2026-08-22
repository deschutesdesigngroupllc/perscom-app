<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Jobs\Central;

use App\Jobs\Central\BackupDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Central\CentralTestCase;

class BackupDatabaseTest extends CentralTestCase
{
    public function test_it_targets_the_backup_queue_on_the_central_connection(): void
    {
        $job = new BackupDatabase;

        $this->assertSame('backup', $job->queue);
        $this->assertSame('central', $job->connection);
    }

    public function test_it_runs_the_backup_command_scoped_to_the_database(): void
    {
        $captured = null;

        Artisan::shouldReceive('call')
            ->once()
            ->andReturnUsing(function (string $command, array $parameters) use (&$captured): int {
                $captured = [$command, $parameters];

                return 0;
            });

        (new BackupDatabase)->handle();

        $this->assertSame('backup:run', $captured[0]);
        $this->assertTrue($captured[1]['--only-db']);
        $this->assertSame('backups', $captured[1]['--only-to-disk']);
        $this->assertSame(['mysql'], config('backup.backup.source.databases'));
    }
}
