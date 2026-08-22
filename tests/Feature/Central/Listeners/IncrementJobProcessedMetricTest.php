<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Listeners;

use App\Listeners\IncrementJobProcessedMetric;
use App\Metrics\JobProcessedMetric;
use App\Metrics\Metric;
use Illuminate\Queue\Events\JobProcessed;
use Tests\Feature\Central\CentralTestCase;

class IncrementJobProcessedMetricTest extends CentralTestCase
{
    public function test_handling_the_event_increments_the_job_processed_metric(): void
    {
        $before = Metric::total(JobProcessedMetric::class);

        $event = new JobProcessed('sync', null);

        (new IncrementJobProcessedMetric)->handle($event);

        $this->assertSame($before + 1, Metric::total(JobProcessedMetric::class));
    }
}
