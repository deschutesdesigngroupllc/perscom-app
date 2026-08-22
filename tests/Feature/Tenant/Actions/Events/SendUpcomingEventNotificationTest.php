<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Events;

use App\Actions\Events\SendUpcomingEventNotification;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Notifications\Tenant\UpcomingEvent;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Tenant\TenantTestCase;

class SendUpcomingEventNotificationTest extends TenantTestCase
{
    public function test_sends_notification_to_registered_users(): void
    {
        Notification::fake();

        $event = Event::factory()
            ->withRegistrations()
            ->withNotifications()
            ->create();

        SendUpcomingEventNotification::handle($event, NotificationInterval::PT1H);

        Notification::assertSentTo($event->registrations->all(), UpcomingEvent::class);
    }

    public function test_does_not_send_when_notifications_are_disabled(): void
    {
        Notification::fake();

        $event = Event::factory()
            ->withRegistrations()
            ->create(['notifications_enabled' => false]);

        SendUpcomingEventNotification::handle($event, NotificationInterval::PT1H);

        Notification::assertNothingSent();
    }

    public function test_can_send_notification_returns_false_without_registrations(): void
    {
        $event = Event::factory()
            ->withNotifications()
            ->create();

        $this->assertFalse(SendUpcomingEventNotification::canSendNotification($event));
    }
}
