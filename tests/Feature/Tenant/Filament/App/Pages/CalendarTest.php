<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Pages;

use App\Filament\App\Pages\Calendar;
use App\Filament\App\Resources\EventResource\Pages\CreateEvent;
use App\Filament\App\Widgets\CalendarWidget;
use App\Models\Calendar as CalendarModel;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\EventRegistration;
use App\Models\Schedule;
use App\Models\User;
use App\Notifications\Tenant\EventOccurrenceCancelled;
use App\Services\EventCalendarService;
use App\Services\UserSettingsService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Testing\TestAction;
use Guava\Calendar\Enums\CalendarViewType;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarTest extends TenantTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
    }

    public function test_it_renders_each_view(): void
    {
        $user = $this->actingAsSuperAdmin();
        Event::factory()->createQuietly(['name' => 'Range Day', 'all_day' => false, 'starts' => Date::parse('2026-06-02 18:00:00'), 'ends' => Date::parse('2026-06-02 19:00:00')]);

        Livewire::test(Calendar::class)
            ->assertOk()
            ->assertSet('display', 'month')
            ->assertSeeHtml('data-calendar-widget')
            ->assertSee('My Events')
            ->assertSee('Open for Registration');

        UserSettingsService::save('calendar_view', 'week', $user);

        Livewire::test(Calendar::class)
            ->assertOk()
            ->assertSet('display', 'week')
            ->assertSeeHtml('data-calendar-widget');

        UserSettingsService::save('calendar_view', 'agenda', $user);

        Livewire::test(Calendar::class)
            ->assertOk()
            ->assertSet('display', 'agenda')
            ->assertDontSeeHtml('data-calendar-widget')
            ->assertSee('Tomorrow')
            ->assertSee('Range Day');
    }

    public function test_the_week_view_uses_the_time_grid(): void
    {
        $this->actingAsSuperAdmin();

        $week = Livewire::test(CalendarWidget::class, ['display' => 'week'])->instance();
        $month = Livewire::test(CalendarWidget::class, ['display' => 'month'])->instance();

        $this->assertInstanceOf(CalendarWidget::class, $week);
        $this->assertInstanceOf(CalendarWidget::class, $month);
        $this->assertSame(CalendarViewType::TimeGridWeek, $week->getCalendarView());
        $this->assertSame(CalendarViewType::DayGridMonth, $month->getCalendarView());
    }

    public function test_the_chosen_view_is_remembered(): void
    {
        $user = $this->actingAsSuperAdmin();

        Livewire::test(Calendar::class)->call('setDisplay', 'week');

        $this->assertSame('week', UserSettingsService::get('calendar_view', flush: true, user: $user));

        Livewire::test(Calendar::class)
            ->assertSet('display', 'week')
            ->assertSet('hasDisplayPreference', true)
            ->call('setDisplay', 'agenda', false);

        $this->assertSame('week', UserSettingsService::get('calendar_view', flush: true, user: $user));
    }

    public function test_invalid_views_are_ignored(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(Calendar::class)
            ->call('setDisplay', 'year')
            ->assertSet('display', 'month');
    }

    public function test_calendar_filter_narrows_the_agenda(): void
    {
        $this->actingAsSuperAdmin();
        $training = CalendarModel::factory()->createQuietly(['name' => 'Training']);
        $operations = CalendarModel::factory()->createQuietly(['name' => 'Operations']);
        Event::factory()->for($training)->createQuietly(['name' => 'Range Day', 'all_day' => false, 'starts' => Date::parse('2026-06-03 18:00:00'), 'ends' => Date::parse('2026-06-03 19:00:00')]);
        Event::factory()->for($operations)->createQuietly(['name' => 'Night Patrol', 'all_day' => false, 'starts' => Date::parse('2026-06-04 18:00:00'), 'ends' => Date::parse('2026-06-04 19:00:00')]);

        Livewire::test(Calendar::class)
            ->call('setDisplay', 'agenda')
            ->assertSee('Range Day')
            ->assertSee('Night Patrol')
            ->call('toggleCalendar', $training->getKey())
            ->assertSet('calendarIds', [$training->getKey()])
            ->assertSee('Range Day')
            ->assertDontSee('Night Patrol')
            ->call('clearFilters')
            ->assertSet('calendarIds', [])
            ->assertSee('Night Patrol');
    }

    public function test_my_events_filter_narrows_the_agenda(): void
    {
        $user = $this->actingAsSuperAdmin();
        $going = Event::factory()->createQuietly(['name' => 'Range Day', 'all_day' => false, 'starts' => Date::parse('2026-06-03 18:00:00'), 'ends' => Date::parse('2026-06-03 19:00:00')]);
        Event::factory()->createQuietly(['name' => 'Night Patrol', 'all_day' => false, 'starts' => Date::parse('2026-06-04 18:00:00'), 'ends' => Date::parse('2026-06-04 19:00:00')]);
        $going->registrations()->attach($user, ['status' => EventRegistrationStatus::GOING]);

        Livewire::test(Calendar::class)
            ->call('setDisplay', 'agenda')
            ->call('toggleMine')
            ->assertSet('mine', true)
            ->assertSee('Range Day')
            ->assertDontSee('Night Patrol');
    }

    public function test_agenda_loads_more_days_and_past_events(): void
    {
        $this->actingAsSuperAdmin();
        Event::factory()->createQuietly(['name' => 'Far Away Event', 'all_day' => false, 'starts' => Date::parse('2026-07-15 18:00:00'), 'ends' => Date::parse('2026-07-15 19:00:00')]);
        Event::factory()->createQuietly(['name' => 'Last Week Event', 'all_day' => false, 'starts' => Date::parse('2026-05-25 18:00:00'), 'ends' => Date::parse('2026-05-25 19:00:00')]);

        Livewire::test(Calendar::class)
            ->call('setDisplay', 'agenda')
            ->assertDontSee('Far Away Event')
            ->assertDontSee('Last Week Event')
            ->call('loadMore')
            ->assertSee('Far Away Event')
            ->call('togglePast')
            ->assertSee('Last Week Event')
            ->assertDontSee('Far Away Event');
    }

    public function test_rsvp_from_the_agenda_records_a_registration(): void
    {
        $user = $this->actingAsSuperAdmin();
        $event = Event::factory()->createQuietly(['name' => 'Range Day', 'all_day' => false, 'registration_enabled' => true, 'starts' => Date::parse('2026-06-03 18:00:00'), 'ends' => Date::parse('2026-06-03 19:00:00')]);

        Livewire::test(Calendar::class)
            ->call('setDisplay', 'agenda')
            ->assertSeeHtml('respondToEvent('.$event->getKey())
            ->call('respondToEvent', $event->getKey(), $event->starts->getTimestamp(), EventRegistrationStatus::GOING->value)
            ->assertDispatched('event-response-updated');

        $this->assertDatabaseHas('events_registrations', [
            'event_id' => $event->getKey(),
            'user_id' => $user->getKey(),
            'status' => EventRegistrationStatus::GOING->value,
        ]);
    }

    public function test_quick_create_persists_an_event_in_the_members_timezone(): void
    {
        $user = $this->actingAsSuperAdmin();
        UserSettingsService::save('timezone', 'America/Chicago', $user);
        $calendar = CalendarModel::factory()->createQuietly();

        Livewire::test(Calendar::class)
            ->callAction('quickCreate', [
                'name' => 'Range Day',
                'calendar_id' => $calendar->getKey(),
                'all_day' => false,
                'starts' => '2026-06-10 18:00:00',
                'ends' => '2026-06-10 20:00:00',
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        $event = Event::query()->where('name', 'Range Day')->sole();

        $this->assertSame($calendar->getKey(), $event->calendar_id);
        $this->assertSame($user->getKey(), $event->author_id);
        $this->assertSame('2026-06-10 23:00:00', $event->starts->utc()->toDateTimeString());
        $this->assertSame('2026-06-11 01:00:00', $event->ends?->utc()->toDateTimeString());
    }

    public function test_selecting_a_range_opens_quick_create_prefilled(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(Calendar::class)
            ->dispatch('calendar-range-selected', starts: '2026-06-10', ends: '2026-06-12', allDay: true)
            ->assertActionMounted('quickCreate')
            ->assertSchemaStateSet(['all_day' => true, 'starts' => '2026-06-10', 'ends' => '2026-06-12']);
    }

    public function test_more_options_prefills_the_full_create_page(): void
    {
        $user = $this->actingAsSuperAdmin();
        UserSettingsService::save('timezone', 'America/Chicago', $user);
        $calendar = CalendarModel::factory()->createQuietly();

        Livewire::withQueryParams(['name' => 'Range Day', 'calendar_id' => $calendar->getKey(), 'starts' => '2026-06-10 23:00:00', 'ends' => '2026-06-11 01:00:00', 'all_day' => 0])
            ->test(CreateEvent::class)
            ->assertSchemaStateSet([
                'name' => 'Range Day',
                'calendar_id' => $calendar->getKey(),
                'starts' => '2026-06-10 23:00:00',
                'ends' => '2026-06-11 01:00:00',
            ]);
    }

    public function test_cancelling_a_date_marks_it_cancelled_and_notifies_members_who_planned_to_attend(): void
    {
        Notification::fake();

        $this->actingAsSuperAdmin();
        $event = $this->createWeeklyEvent();
        $starts = Date::parse('2026-06-11 20:00:00');

        $going = User::factory()->createQuietly();
        $maybe = User::factory()->createQuietly();
        $notGoing = User::factory()->createQuietly();
        $event->registrations()->attach($going, ['status' => EventRegistrationStatus::GOING]);
        $event->registrations()->attach($notGoing, ['status' => EventRegistrationStatus::GOING]);
        $occurrence = EventOccurrence::factory()->for($event)->createQuietly(['starts' => $starts, 'ends' => $starts->copy()->addHours(2)]);
        EventRegistration::query()->forceCreate(['event_id' => $event->getKey(), 'user_id' => $maybe->getKey(), 'event_occurrence_id' => $occurrence->getKey(), 'status' => EventRegistrationStatus::TENTATIVE]);
        EventRegistration::query()->forceCreate(['event_id' => $event->getKey(), 'user_id' => $notGoing->getKey(), 'event_occurrence_id' => $occurrence->getKey(), 'status' => EventRegistrationStatus::NOT_GOING]);

        Livewire::test(Calendar::class)
            ->dispatch('calendar-occurrence-selected', eventId: $event->getKey(), starts: $starts->getTimestamp())
            ->assertActionMounted('occurrence');

        Livewire::test(Calendar::class)
            ->mountAction(TestAction::make('occurrence')->arguments(['event' => $event->getKey(), 'starts' => $starts->getTimestamp()]))
            ->assertMountedActionModalSee(['Weekly Ops', 'Thursday, June 11, 2026', 'Cancel This Date', 'Open Event']);

        Livewire::test(Calendar::class)
            ->callAction([
                TestAction::make('occurrence')->arguments(['event' => $event->getKey(), 'starts' => $starts->getTimestamp()]),
                TestAction::make('cancelOccurrence'),
            ], ['reason' => 'Range closed for maintenance.'])
            ->assertHasNoErrors()
            ->assertDispatched('calendar--refresh');

        $occurrence->refresh();

        $this->assertNotNull($occurrence->cancelled_at);
        $this->assertSame('Range closed for maintenance.', $occurrence->notes);

        Notification::assertSentTo([$going, $maybe], EventOccurrenceCancelled::class);
        Notification::assertNotSentTo($notGoing, EventOccurrenceCancelled::class);

        $calendarDates = EventCalendarService::occurrences(Date::parse('2026-06-01'), Date::parse('2026-06-21'));
        $embedDates = EventCalendarService::occurrences(Date::parse('2026-06-01'), Date::parse('2026-06-21'), includeCancelled: false);

        $this->assertTrue($calendarDates->firstWhere(fn ($date): bool => $date->starts->equalTo($starts))?->isCancelled());
        $this->assertNull($embedDates->first(fn ($date): bool => $date->starts->equalTo($starts)));
    }

    public function test_restoring_a_date_reverses_the_cancellation(): void
    {
        $this->actingAsSuperAdmin();
        $event = $this->createWeeklyEvent();
        $starts = Date::parse('2026-06-11 20:00:00');
        $occurrence = EventOccurrence::factory()->for($event)->cancelled()->createQuietly(['starts' => $starts, 'ends' => $starts->copy()->addHours(2), 'notes' => 'Weather']);

        Livewire::test(Calendar::class)
            ->assertActionHidden([
                TestAction::make('occurrence')->arguments(['event' => $event->getKey(), 'starts' => $starts->getTimestamp()]),
                TestAction::make('cancelOccurrence'),
            ])
            ->callAction([
                TestAction::make('occurrence')->arguments(['event' => $event->getKey(), 'starts' => $starts->getTimestamp()]),
                TestAction::make('restoreOccurrence'),
            ]);

        $occurrence->refresh();

        $this->assertNull($occurrence->cancelled_at);
        $this->assertNull($occurrence->notes);
    }

    public function test_members_without_update_permission_cannot_cancel_a_date(): void
    {
        Notification::fake();

        $member = User::factory()->createQuietly();
        $member->givePermissionTo(
            Permission::findOrCreate('view_event'),
            Permission::findOrCreate('view_any_event'),
            Permission::findOrCreate('view_calendar'),
        );
        $this->actingAs($member);

        $event = $this->createWeeklyEvent();
        $starts = Date::parse('2026-06-11 20:00:00');

        Livewire::test(Calendar::class)
            ->assertActionHidden('quickCreate')
            ->assertActionHidden([
                TestAction::make('occurrence')->arguments(['event' => $event->getKey(), 'starts' => $starts->getTimestamp()]),
                TestAction::make('cancelOccurrence'),
            ]);

        $this->assertFalse(EventOccurrence::query()->where('event_id', $event->getKey())->whereNotNull('cancelled_at')->exists());
        Notification::assertNothingSent();
    }

    private function createWeeklyEvent(): Event
    {
        $start = Date::parse('2026-06-04 20:00:00');
        $event = Event::factory()->createQuietly(['name' => 'Weekly Ops', 'repeats' => true, 'all_day' => false, 'starts' => $start, 'ends' => $start->copy()->addHours(2)]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 2, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        return $event->refresh();
    }

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }
}
