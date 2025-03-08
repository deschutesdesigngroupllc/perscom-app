<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Event;
use App\Services\ScheduleService;
use RRule\RRule;
use Tests\Feature\Tenant\TenantTestCase;

class EventServiceTest extends TenantTestCase
{
    public function test_it_can_generate_a_recurring_rule(): void
    {
        $event = Event::factory()->withSchedule()->createQuietly();

        $rule = ScheduleService::generateRecurringRule($event->schedule);

        $this->assertInstanceOf(RRule::class, $rule);
    }
}
