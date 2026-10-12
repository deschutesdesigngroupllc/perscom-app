<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Widgets;

use App\Filament\App\Widgets\UpcomingEventsWidget;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\EventRegistration;
use App\Models\Schedule;
use App\Models\User;
use App\Services\UserSettingsService;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class UpcomingEventsWidgetTest extends TenantTestCase
{
    public function test_it_features_the_next_occurrence_and_lists_the_rest_of_the_week(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAs($this->member());

        $this->makeOneTimeEvent(now()->addDays(3), ['name' => 'Range Day']);
        $this->makeOneTimeEvent(now()->addDay(), ['name' => 'Squad Briefing']);
        $this->makeOneTimeEvent(now()->addDays(10), ['name' => 'Next Month Exercise']);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertOk()
            ->assertSee('Up Next')
            ->assertSeeInOrder(['Starts in 1 day', 'Squad Briefing', 'Range Day'])
            ->assertDontSee('Next Month Exercise');
    }

    public function test_it_shows_the_next_date_of_a_repeating_event(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAs($this->member());

        $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'), ['name' => 'Weekly Drill']);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSee('Weekly Drill')
            ->assertSee($this->localDay(Date::parse('2026-06-04 20:00:00')));
    }

    public function test_it_hides_cancelled_occurrences(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAs($this->member());

        $event = $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'), ['name' => 'Weekly Drill']);
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly(['starts' => Date::parse('2026-06-04 20:00:00'), 'ends' => Date::parse('2026-06-04 21:00:00')]);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertDontSee('Weekly Drill')
            ->assertSee('Nothing scheduled this week');
    }

    public function test_responding_to_a_one_time_event_records_a_series_registration(): void
    {
        $user = $this->member();
        $this->actingAs($user);
        $event = $this->makeOneTimeEvent(now()->addDays(2));

        Livewire::test(UpcomingEventsWidget::class)
            ->call('respondToEvent', $event->getKey(), $event->starts->getTimestamp(), EventRegistrationStatus::GOING->value)
            ->assertDispatched('event-response-updated');

        $registration = EventRegistration::query()->where('event_id', $event->getKey())->where('user_id', $user->getKey())->sole();

        $this->assertSame(EventRegistrationStatus::GOING, $registration->status);
        $this->assertNull($registration->event_occurrence_id);
    }

    public function test_responding_to_a_repeating_event_records_a_registration_for_that_date(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $user = $this->member();
        $this->actingAs($user);
        $event = $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'));
        $starts = Date::parse('2026-06-04 20:00:00');

        Livewire::test(UpcomingEventsWidget::class)
            ->call('respondToEvent', $event->getKey(), $starts->getTimestamp(), EventRegistrationStatus::TENTATIVE->value)
            ->assertSee('aria-pressed="true"', false);

        $registration = EventRegistration::query()->where('event_id', $event->getKey())->where('user_id', $user->getKey())->sole();

        $this->assertSame(EventRegistrationStatus::TENTATIVE, $registration->status);
        $this->assertNotNull($registration->event_occurrence_id);
        $this->assertTrue($registration->occurrence->starts->equalTo($starts));
    }

    public function test_unanswered_occurrences_are_marked(): void
    {
        $this->actingAs($this->member());

        $this->makeOneTimeEvent(now()->addDay(), ['name' => 'Squad Briefing']);
        $this->makeOneTimeEvent(now()->addDays(2), ['name' => 'Range Day']);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSeeHtml('</span> Respond');
    }

    public function test_check_in_is_offered_only_while_the_window_is_open(): void
    {
        $user = $this->member();
        $this->actingAs($user);

        $this->makeOneTimeEvent(now()->addDays(2), ['name' => 'Range Day']);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSee('Range Day')
            ->assertDontSee('Check In');

        $this->makeOneTimeEvent(now()->addMinutes(30), ['name' => 'Squad Briefing']);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSee('Squad Briefing')
            ->assertSee('Check In');
    }

    public function test_check_in_is_hidden_once_the_member_has_attended(): void
    {
        $user = $this->member();
        $this->actingAs($user);

        $event = $this->makeOneTimeEvent(now()->subMinutes(10), ['name' => 'Squad Briefing']);
        $event->registrations()->attach($user, ['status' => EventRegistrationStatus::ATTENDED->value]);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSee('Squad Briefing')
            ->assertSee('Happening now')
            ->assertDontSee('Check In');
    }

    public function test_it_shows_an_empty_state(): void
    {
        $this->actingAs($this->member());

        Livewire::test(UpcomingEventsWidget::class)
            ->assertSee('Nothing scheduled this week')
            ->assertSee('Subscribe');
    }

    public function test_members_who_cannot_view_events_see_nothing(): void
    {
        $this->actingAs(User::factory()->createQuietly());

        $this->makeOneTimeEvent(now()->addDay(), ['name' => 'Squad Briefing']);

        Livewire::test(UpcomingEventsWidget::class)
            ->assertDontSee('Squad Briefing')
            ->assertSee('Nothing scheduled this week');
    }

    public function test_the_subscribe_action_opens_from_the_widget(): void
    {
        $this->actingAs($this->member());

        Livewire::test(UpcomingEventsWidget::class)
            ->mountAction('subscribe')
            ->assertActionMounted('subscribe')
            ->assertMountedActionModalSee(['Subscribe to Calendar', 'calendar.ics', 'Google', 'Outlook']);
    }

    private function member(): User
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(['view_any_event', 'view_event']);

        return $user;
    }

    private function localDay(CarbonInterface $date): string
    {
        return $date->copy()->setTimezone(UserSettingsService::timezone())->format('l, F j');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeOneTimeEvent(CarbonInterface $starts, array $attributes = []): Event
    {
        return Event::factory()->createQuietly([
            'all_day' => false,
            'repeats' => false,
            'starts' => $starts,
            'ends' => $starts->copy()->addHour(),
            'url' => null,
            'registration_enabled' => true,
            'registration_deadline' => null,
            ...$attributes,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeWeeklyEvent(CarbonInterface $start, array $attributes = []): Event
    {
        $event = Event::factory()->createQuietly([
            'all_day' => false,
            'repeats' => true,
            'starts' => $start,
            'ends' => $start->copy()->addHour(),
            'registration_enabled' => true,
            'registration_deadline' => null,
            ...$attributes,
        ]);

        Schedule::factory()->for($event, 'repeatable')->createQuietly([
            'start' => $start,
            'duration' => 1,
            'frequency' => ScheduleFrequency::WEEKLY,
            'interval' => 1,
            'end_type' => ScheduleEndType::NEVER,
        ]);

        return $event->refresh();
    }
}
