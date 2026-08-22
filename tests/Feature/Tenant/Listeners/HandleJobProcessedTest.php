<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\HandleJobProcessed;
use App\Models\JobHistory;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Jobs\RedisJob;
use Mockery;
use Tests\Feature\Tenant\TenantTestCase;

class HandleJobProcessedTest extends TenantTestCase
{
    public function test_it_records_job_history_for_a_redis_job(): void
    {
        $commandName = 'App\\Jobs\\Tenant\\CalculateSchedules';

        $job = Mockery::mock(RedisJob::class);
        $job->shouldReceive('payload')->andReturn([
            'data' => ['commandName' => $commandName],
        ]);

        $event = new JobProcessed('redis', $job);

        new HandleJobProcessed()->handle($event);

        $this->assertDatabaseHas('job_history', [
            'job' => $commandName,
        ]);

        $this->assertNotNull(JobHistory::query()->where('job', $commandName)->value('finished_at'));
    }

    public function test_it_updates_existing_job_history_row(): void
    {
        $commandName = 'App\\Jobs\\Tenant\\BackupDatabase';

        JobHistory::query()->create([
            'job' => $commandName,
            'finished_at' => now()->subDay(),
        ]);

        $job = Mockery::mock(RedisJob::class);
        $job->shouldReceive('payload')->andReturn([
            'data' => ['commandName' => $commandName],
        ]);

        new HandleJobProcessed()->handle(new JobProcessed('redis', $job));

        $this->assertSame(1, JobHistory::query()->where('job', $commandName)->count());
    }

    public function test_it_ignores_non_redis_jobs(): void
    {
        $job = Mockery::mock(Job::class);

        new HandleJobProcessed()->handle(new JobProcessed('sync', $job));

        $this->assertSame(0, JobHistory::query()->count());
    }
}
