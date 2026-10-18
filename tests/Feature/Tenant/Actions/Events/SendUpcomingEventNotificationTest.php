<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Actions\Events;

use App\Actions\Events\SendUpcomingEventNotification;
use App\Data\Events\OccurrenceData;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\Tenant\UpcomingEvent;
use App\Services\EventCalendarService;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use Illuminate\Support\Facades\Date;
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

    public function test_reminds_members_by_their_response_for_the_upcoming_occurrence(): void
    {
        Notification::fake();

        $event = $this->weeklyEvent();
        $occurrence = EventOccurrenceService::nearest($event->fresh(), $event->schedule->next_occurrence);
        $this->assertInstanceOf(OccurrenceData::class, $occurrence);

        $goingThisDate = User::factory()->createQuietly();
        $maybeSeries = User::factory()->createQuietly();
        $goingSeriesButNotThisDate = User::factory()->createQuietly();
        $notGoingSeries = User::factory()->createQuietly();

        EventRegistrationService::respond($occurrence, $goingThisDate, EventRegistrationStatus::GOING);
        EventRegistrationService::respond($occurrence, $maybeSeries, EventRegistrationStatus::TENTATIVE, wholeSeries: true);
        EventRegistrationService::respond($occurrence, $goingSeriesButNotThisDate, EventRegistrationStatus::GOING, wholeSeries: true);
        EventRegistrationService::respond($occurrence, $goingSeriesButNotThisDate, EventRegistrationStatus::NOT_GOING);
        EventRegistrationService::respond($occurrence, $notGoingSeries, EventRegistrationStatus::NOT_GOING, wholeSeries: true);

        SendUpcomingEventNotification::handle($event->fresh(), NotificationInterval::PT1H);

        Notification::assertSentTo([$goingThisDate, $maybeSeries], UpcomingEvent::class);
        Notification::assertNotSentTo([$goingSeriesButNotThisDate, $notGoingSeries], UpcomingEvent::class);
    }

    public function test_can_send_notification_when_only_per_date_responses_exist(): void
    {
        $event = $this->weeklyEvent();
        $occurrence = EventOccurrenceService::nearest($event->fresh(), $event->schedule->next_occurrence);
        $this->assertInstanceOf(OccurrenceData::class, $occurrence);

        EventRegistrationService::respond($occurrence, User::factory()->createQuietly(), EventRegistrationStatus::GOING);

        $this->assertTrue(SendUpcomingEventNotification::canSendNotification($event->fresh()));
    }

    public function test_can_send_notification_returns_false_when_no_one_is_going(): void
    {
        $event = $this->weeklyEvent();
        $occurrence = EventOccurrenceService::nearest($event->fresh(), $event->schedule->next_occurrence);
        $this->assertInstanceOf(OccurrenceData::class, $occurrence);

        EventRegistrationService::respond($occurrence, User::factory()->createQuietly(), EventRegistrationStatus::NOT_GOING, wholeSeries: true);

        $this->assertFalse(SendUpcomingEventNotification::canSendNotification($event->fresh()));
    }

    public function test_does_not_remind_members_when_the_upcoming_occurrence_is_cancelled(): void
    {
        Notification::fake();

        $event = $this->weeklyEvent();
        $occurrence = EventOccurrenceService::nearest($event->fresh(), $event->schedule->next_occurrence);
        $this->assertInstanceOf(OccurrenceData::class, $occurrence);

        $member = User::factory()->createQuietly();
        EventRegistrationService::respond($occurrence, $member, EventRegistrationStatus::GOING, wholeSeries: true);
        EventCalendarService::cancel($occurrence);

        SendUpcomingEventNotification::handle($event->fresh(), NotificationInterval::PT1H);

        Notification::assertNotSentTo($member, UpcomingEvent::class);
    }

    private function weeklyEvent(): Event
    {
        $this->travelTo(Date::parse('2026-06-20 12:00:00'));

        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly([
            'repeats' => true,
            'all_day' => false,
            'starts' => $start,
            'ends' => $start->copy()->addHour(),
            'registration_enabled' => true,
            'registration_deadline' => null,
            'notifications_enabled' => true,
            'notifications_interval' => [NotificationInterval::PT1H],
            'notifications_channels' => [NotificationChannel::MAIL],
        ]);
        Schedule::factory()->for($event, 'repeatable')->create(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        return $event->fresh();
    }
}
