<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Widgets\CalendarWidget;
use App\Livewire\Widgets\Calendar as CalendarEmbed;
use App\Models\Calendar;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Schedule;
use App\Models\User;
use BezhanSalleh\FilamentShield\Support\Utils;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarWidgetTest extends TenantTestCase
{
    public function test_it_shows_each_occurrence_with_cancelled_ones_struck_through(): void
    {
        $this->actingAsSuperAdmin();
        $this->createWeeklyEventWithCancelledDate();

        $events = $this->eventsFor(CalendarWidget::class);

        $this->assertSame(['Weekly Ops', 'Weekly Ops', 'Weekly Ops'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => (string) $calendarEvent->getTitle())->all());
        $this->assertSame(['2026-06-04 20:00', '2026-06-11 20:00', '2026-06-18 20:00'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => $calendarEvent->getStart()->utc()->format('Y-m-d H:i'))->all());
        $this->assertSame('2026-06-04 22:00', $events[0]->getEnd()->utc()->format('Y-m-d H:i'));

        $this->assertFalse($events[0]->getExtendedProps()['cancelled']);
        $this->assertTrue($events[1]->getExtendedProps()['cancelled']);
        $this->assertStringContainsString('line-through', $events[1]->getStyles());
        $this->assertStringNotContainsString('line-through', $events[0]->getStyles());
    }

    public function test_the_public_embed_hides_cancelled_occurrences(): void
    {
        $this->actingAsSuperAdmin();
        $this->createWeeklyEventWithCancelledDate();

        $events = $this->eventsFor(CalendarEmbed::class);

        $this->assertSame(['2026-06-04 20:00', '2026-06-18 20:00'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => $calendarEvent->getStart()->utc()->format('Y-m-d H:i'))->all());
    }

    public function test_pills_are_filled_when_going_and_outlined_when_unanswered(): void
    {
        $user = $this->actingAsSuperAdmin();
        $calendar = Calendar::factory()->createQuietly(['color' => '#16a34a']);
        $going = Event::factory()->for($calendar)->createQuietly(['name' => 'Going Event', 'all_day' => false, 'starts' => Date::parse('2026-06-05 18:00:00'), 'ends' => Date::parse('2026-06-05 19:00:00')]);
        Event::factory()->for($calendar)->createQuietly(['name' => 'Unanswered Event', 'all_day' => false, 'starts' => Date::parse('2026-06-06 18:00:00'), 'ends' => Date::parse('2026-06-06 19:00:00')]);
        $going->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING]);

        $events = collect($this->eventsFor(CalendarWidget::class))->keyBy(fn (CalendarEvent $calendarEvent): string => (string) $calendarEvent->getTitle());

        $this->assertSame('#16a34a', $events['Going Event']->getBackgroundColor());
        $this->assertSame('going', $events['Going Event']->getExtendedProps()['response']);
        $this->assertStringNotContainsString('box-shadow', $events['Going Event']->getStyles());

        $this->assertStringContainsString('transparent', (string) $events['Unanswered Event']->getBackgroundColor());
        $this->assertSame('#16a34a', $events['Unanswered Event']->getTextColor());
        $this->assertStringContainsString('box-shadow', $events['Unanswered Event']->getStyles());
        $this->assertNull($events['Unanswered Event']->getExtendedProps()['response']);
    }

    public function test_calendar_filter_narrows_occurrences(): void
    {
        $this->actingAsSuperAdmin();
        $training = Calendar::factory()->createQuietly(['name' => 'Training']);
        $operations = Calendar::factory()->createQuietly(['name' => 'Operations']);
        Event::factory()->for($training)->createQuietly(['name' => 'Range Day', 'all_day' => false, 'starts' => Date::parse('2026-06-05 18:00:00'), 'ends' => Date::parse('2026-06-05 19:00:00')]);
        Event::factory()->for($operations)->createQuietly(['name' => 'Patrol', 'all_day' => false, 'starts' => Date::parse('2026-06-06 18:00:00'), 'ends' => Date::parse('2026-06-06 19:00:00')]);

        $events = $this->eventsFor(CalendarWidget::class, ['calendarIds' => [$training->getKey()]]);

        $this->assertSame(['Range Day'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => (string) $calendarEvent->getTitle())->all());
    }

    public function test_my_events_filter_only_shows_events_the_member_is_attending(): void
    {
        $user = $this->actingAsSuperAdmin();
        $going = Event::factory()->createQuietly(['name' => 'Going Event', 'all_day' => false, 'starts' => Date::parse('2026-06-05 18:00:00'), 'ends' => Date::parse('2026-06-05 19:00:00')]);
        $maybe = Event::factory()->createQuietly(['name' => 'Maybe Event', 'all_day' => false, 'starts' => Date::parse('2026-06-06 18:00:00'), 'ends' => Date::parse('2026-06-06 19:00:00')]);
        $declined = Event::factory()->createQuietly(['name' => 'Declined Event', 'all_day' => false, 'starts' => Date::parse('2026-06-07 18:00:00'), 'ends' => Date::parse('2026-06-07 19:00:00')]);
        Event::factory()->createQuietly(['name' => 'Other Event', 'all_day' => false, 'starts' => Date::parse('2026-06-08 18:00:00'), 'ends' => Date::parse('2026-06-08 19:00:00')]);

        $going->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING]);
        $maybe->registrations()->attach($user, ['status' => EventRegistrationStatus::TENTATIVE]);
        $declined->registrations()->attach($user, ['status' => EventRegistrationStatus::NOT_GOING]);

        $events = $this->eventsFor(CalendarWidget::class, ['mine' => true]);

        $this->assertSame(['Going Event', 'Maybe Event'], collect($events)->map(fn (CalendarEvent $calendarEvent): string => (string) $calendarEvent->getTitle())->all());
    }

    public function test_clicking_an_occurrence_on_the_calendar_page_opens_it_instead_of_redirecting(): void
    {
        $this->actingAsSuperAdmin();
        $event = Event::factory()->createQuietly(['name' => 'Range Day', 'all_day' => false, 'starts' => Date::parse('2026-06-05 18:00:00'), 'ends' => Date::parse('2026-06-05 19:00:00')]);

        $component = Livewire::test(CalendarWidget::class, ['onCalendarPage' => true]);
        $widget = $component->instance();
        $this->assertInstanceOf(CalendarWidget::class, $widget);

        $payload = $widget->getEventsJs(['startStr' => '2026-06-01T00:00:00Z', 'endStr' => '2026-06-21T00:00:00Z', 'tzOffset' => 0]);

        $component->call('onEventClickJs', ['event' => $payload[0], 'view' => ['type' => 'dayGridMonth', 'title' => 'June 2026', 'currentStart' => '2026-06-01T00:00:00Z', 'currentEnd' => '2026-07-01T00:00:00Z', 'activeStart' => '2026-05-31T00:00:00Z', 'activeEnd' => '2026-07-12T00:00:00Z'], 'tzOffset' => 0])
            ->assertDispatched('calendar-occurrence-selected', eventId: $event->getKey(), starts: $event->starts->getTimestamp())
            ->assertNoRedirect();
    }

    /**
     * @param  class-string<CalendarWidget>  $widget
     * @param  array<string, mixed>  $parameters
     * @return array<int, CalendarEvent>
     */
    private function eventsFor(string $widget, array $parameters = []): array
    {
        $instance = Livewire::test($widget, $parameters)->instance();
        $this->assertInstanceOf(CalendarWidget::class, $instance);

        /** @var array<int, CalendarEvent> $events */
        $events = (fn (): array => $this->getEvents(['start' => '2026-06-01', 'end' => '2026-06-21']))->call($instance);

        return $events;
    }

    private function createWeeklyEventWithCancelledDate(): Event
    {
        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly(['name' => 'Weekly Ops', 'repeats' => true, 'all_day' => false, 'starts' => $start, 'ends' => $start->copy()->addHour()]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 2, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly(['starts' => Date::parse('2026-06-11 20:00:00'), 'ends' => Date::parse('2026-06-11 22:00:00')]);
        Event::factory()->createQuietly(['name' => 'Old Event', 'repeats' => false, 'all_day' => false, 'starts' => Date::parse('2026-01-01 10:00:00'), 'ends' => Date::parse('2026-01-01 11:00:00')]);

        return $event;
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
