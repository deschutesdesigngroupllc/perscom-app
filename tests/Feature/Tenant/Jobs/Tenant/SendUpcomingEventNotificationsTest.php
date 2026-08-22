<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Jobs\Tenant;

use App\Jobs\Tenant\SendUpcomingEventNotifications;
use App\Models\Event;
use App\Notifications\Tenant\UpcomingEvent;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class SendUpcomingEventNotificationsTest extends TenantTestCase
{
    public function test_it_notifies_registrants_when_the_interval_falls_within_the_next_day(): void
    {
        Notification::fake();

        $event = Event::factory()
            ->withSchedule()
            ->withNotifications()
            ->withRegistrations()
            ->create();

        // Schedule two hours out so the one-hour interval notification lands
        // inside the next 24 hours the job checks.
        $event->schedule->update(['start' => now()->addHours(2)]);

        (new SendUpcomingEventNotifications)->work();

        $registrant = $event->registrations->first();

        Notification::assertSentTo($registrant, UpcomingEvent::class);
    }

    public function test_it_ignores_non_repeating_events(): void
    {
        Notification::fake();

        Event::factory()
            ->withNotifications()
            ->withRegistrations()
            ->create(['repeats' => false]);

        (new SendUpcomingEventNotifications)->work();

        Notification::assertNothingSent();
    }
}
