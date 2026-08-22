<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Listeners;

use App\Listeners\IncrementJobFailedMetric;
use App\Metrics\JobFailedMetric;
use App\Metrics\Metric;
use Illuminate\Queue\Events\JobFailed;
use RuntimeException;
use Tests\Feature\Central\CentralTestCase;

class IncrementJobFailedMetricTest extends CentralTestCase
{
    public function test_handling_the_event_increments_the_job_failed_metric(): void
    {
        $before = Metric::total(JobFailedMetric::class);

        $event = new JobFailed('sync', null, new RuntimeException('Job blew up'));

        (new IncrementJobFailedMetric)->handle($event);

        $this->assertSame($before + 1, Metric::total(JobFailedMetric::class));
    }
}
