<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Metrics\Metric;
use App\Metrics\UserLoginMetric;
use Illuminate\Auth\Events\Login;

class IncrementUserLoginMetric
{
    public function handle(Login $event): void
    {
        Metric::increment(UserLoginMetric::class);
    }
}
