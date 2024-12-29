<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Metrics\JobFailedMetric;
use App\Metrics\Metric;
use Illuminate\Queue\Events\JobFailed;

class IncrementJobFailedMetric
{
    public function handle(JobFailed $event): void
    {
        Metric::increment(JobFailedMetric::class);
    }
}
