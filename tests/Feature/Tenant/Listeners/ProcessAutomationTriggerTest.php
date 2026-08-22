<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Contracts\AutomationTriggerable;
use App\Events\Automations\UserCreated;
use App\Listeners\ProcessAutomationTrigger;
use App\Models\User;
use App\Services\AutomationService;
use Mockery;
use Tests\Feature\Tenant\TenantTestCase;

class ProcessAutomationTriggerTest extends TenantTestCase
{
    public function test_handle_passes_the_event_to_the_automation_service(): void
    {
        $user = User::factory()->createQuietly();
        $event = new UserCreated($user);

        $service = Mockery::spy(AutomationService::class);

        $listener = new ProcessAutomationTrigger($service);
        $listener->handle($event);

        $service->shouldHaveReceived('process')
            ->once()
            ->with($event);
    }

    public function test_handle_accepts_any_automation_triggerable_event(): void
    {
        $event = Mockery::mock(AutomationTriggerable::class);

        $service = Mockery::spy(AutomationService::class);

        $listener = new ProcessAutomationTrigger($service);
        $listener->handle($event);

        $service->shouldHaveReceived('process')
            ->once()
            ->with($event);
    }
}
