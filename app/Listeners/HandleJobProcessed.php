<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\JobHistory;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Jobs\RedisJob;

class HandleJobProcessed
{
    public function handle(JobProcessed $event): void
    {
        if (! tenancy()->initialized) {
            return;
        }

        $job = $event->job;

        if ($job instanceof RedisJob) {
            $commandName = data_get($job->payload(), 'data.commandName');

            JobHistory::updateOrCreate([
                'job' => $commandName,
            ], [
                'finished_at' => now(),
            ]);
        }
    }
}
