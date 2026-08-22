<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\PruneApiPurgeLogs;
use App\Models\Activity;
use Tests\Feature\Tenant\TenantTestCase;

class PruneApiPurgeLogsTest extends TenantTestCase
{
    public function test_it_deletes_api_purge_logs_older_than_the_cutoff(): void
    {
        $old = Activity::factory()->create([
            'log_name' => 'api_purge',
            'created_at' => now()->subDays(60),
        ]);

        (new PruneApiPurgeLogs(days: 30))->work();

        $this->assertDatabaseMissing('activity_log', ['id' => $old->getKey()]);
    }

    public function test_it_retains_api_purge_logs_within_the_cutoff(): void
    {
        $recent = Activity::factory()->create([
            'log_name' => 'api_purge',
            'created_at' => now()->subDays(5),
        ]);

        (new PruneApiPurgeLogs(days: 30))->work();

        $this->assertDatabaseHas('activity_log', ['id' => $recent->getKey()]);
    }

    public function test_the_cutoff_window_is_configurable(): void
    {
        $log = Activity::factory()->create([
            'log_name' => 'api_purge',
            'created_at' => now()->subDays(5),
        ]);

        (new PruneApiPurgeLogs(days: 1))->work();

        $this->assertDatabaseMissing('activity_log', ['id' => $log->getKey()]);
    }
}
