<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Event;
use App\Services\EventService;
use RRule\RRule;
use Tests\Feature\Tenant\TenantTestCase;

class EventServiceTest extends TenantTestCase
{
    public function test_it_can_generate_a_recurring_rule()
    {
        $event = Event::factory()->recurring()->create();

        $rule = EventService::generateRecurringRule($event);

        $this->assertInstanceOf(RRule::class, $rule);
    }
}
