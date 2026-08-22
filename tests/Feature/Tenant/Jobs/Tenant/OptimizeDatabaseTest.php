<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\OptimizeDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class OptimizeDatabaseTest extends TenantTestCase
{
    public function test_work_skips_table_optimization_on_non_mysql_drivers(): void
    {
        $db = DB::partialMock();
        $db->shouldReceive('getDriverName')->andReturn('sqlite');
        $db->shouldNotReceive('select');
        $db->shouldNotReceive('statement');

        new OptimizeDatabase($this->tenant->getKey())->work();

        $this->addToAssertionCount(1);
    }

    public function test_it_is_routed_to_the_clean_queue_when_tenancy_is_enabled(): void
    {
        Queue::fake();
        config(['tenancy.enabled' => true]);

        dispatch(new OptimizeDatabase($this->tenant->getKey()));

        Queue::assertPushed(
            OptimizeDatabase::class,
            fn (OptimizeDatabase $job): bool => $job->queue === 'clean'
                && $job->connection === 'central'
                && $job->tenantKey === $this->tenant->getKey()
        );
    }
}
