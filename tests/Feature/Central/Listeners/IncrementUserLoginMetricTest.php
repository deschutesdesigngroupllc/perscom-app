<?php

declare(strict_types=1);

namespace Tests\Feature\Central\Listeners;

use App\Listeners\IncrementUserLoginMetric;
use App\Metrics\Metric;
use App\Metrics\UserLoginMetric;
use App\Models\Admin;
use Illuminate\Auth\Events\Login;
use Tests\Feature\Central\CentralTestCase;

class IncrementUserLoginMetricTest extends CentralTestCase
{
    public function test_handling_the_event_increments_the_user_login_metric(): void
    {
        $before = Metric::total(UserLoginMetric::class);

        $event = new Login('web', Admin::factory()->create(), false);

        (new IncrementUserLoginMetric)->handle($event);

        $this->assertSame($before + 1, Metric::total(UserLoginMetric::class));
    }
}
