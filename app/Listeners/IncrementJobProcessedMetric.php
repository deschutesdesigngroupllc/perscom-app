<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Metrics\JobProcessedMetric;
use App\Metrics\Metric;
use Illuminate\Queue\Events\JobProcessed;

class IncrementJobProcessedMetric
{
    public function handle(JobProcessed $event): void
    {
        Metric::increment(JobProcessedMetric::class);
    }
}
