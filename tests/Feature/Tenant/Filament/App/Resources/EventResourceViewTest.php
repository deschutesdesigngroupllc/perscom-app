<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Filament\App\Resources;

use App\Filament\App\Resources\EventResource\Pages\ViewEvent;
use App\Filament\App\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Permission;
use App\Models\Schedule;
use App\Models\User;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use BezhanSalleh\FilamentShield\Support\Utils;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Date;
use Livewire\Livewire;
use Tests\Feature\Tenant\TenantTestCase;

class EventResourceViewTest extends TenantTestCase
{
    public function test_it_renders_the_hero_briefing_and_whos_going_for_a_one_time_event(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly([
            'name' => 'Range Day',
            'content' => '<p>Bring eye protection.</p>',
            'location' => null,
            'url' => null,
            'all_day' => false,
            'starts' => now()->addDay()->setTime(18, 0),
            'ends' => now()->addDay()->setTime(20, 0),
            'registration_enabled' => true,
            'registration_deadline' => null,
        ]);
        $going = User::factory()->createQuietly(['name' => 'Going Member']);
        $maybe = User::factory()->createQuietly(['name' => 'Maybe Member']);
        $event->registrations()->attach($going, ['status' => EventRegistrationStatus::GOING]);
        $event->registrations()->attach($maybe, ['status' => EventRegistrationStatus::TENTATIVE]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertOk()
            ->assertSeeHtml('id="event-title"')
            ->assertSee('Range Day')
            ->assertSee('Briefing')
            ->assertSee('Bring eye protection.')
            ->assertSee("Who's Going")
            ->assertSee('Going Member')
            ->assertSee('Maybe Member')
            ->assertSee('2 hours')
            ->assertSee('Are you going?')
            ->assertDontSee('Event dates');
    }

    public function test_it_falls_back_to_the_description_when_there_is_no_briefing(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly(['content' => null, 'description' => '<p>Short summary.</p>', 'location' => null]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertSee('Short summary.');
    }

    public function test_it_renders_the_repeat_rule_and_date_picker_for_a_repeating_event(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAsSuperAdmin();

        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertOk()
            ->assertSee('Weekly')
            ->assertSee('Event dates')
            ->assertSee('This date')
            ->assertSee('All dates')
            ->assertSeeHtml('aria-current="date"');
    }

    public function test_the_occurrence_query_parameter_selects_the_date(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAsSuperAdmin();

        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $first = Date::parse('2026-06-04 20:00:00');
        $second = Date::parse('2026-06-11 20:00:00');
        EventRegistrationService::respond(EventOccurrenceService::find($event, $first), User::factory()->createQuietly(['name' => 'First Week Member']), EventRegistrationStatus::GOING);
        EventRegistrationService::respond(EventOccurrenceService::find($event, $second), User::factory()->createQuietly(['name' => 'Second Week Member']), EventRegistrationStatus::GOING);

        Livewire::withQueryParams(['occurrence' => $second->getTimestamp()])
            ->test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertSee('Second Week Member')
            ->assertDontSee('First Week Member')
            ->set('occurrence', $first->getTimestamp())
            ->assertSee('First Week Member')
            ->assertDontSee('Second Week Member');
    }

    public function test_an_unknown_occurrence_falls_back_to_the_nearest_date(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAsSuperAdmin();

        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));

        $page = Livewire::withQueryParams(['occurrence' => Date::parse('2026-06-05 09:00:00')->getTimestamp()])
            ->test(ViewEvent::class, ['record' => $event->getKey()])
            ->instance();

        $this->assertInstanceOf(ViewEvent::class, $page);
        $this->assertSame('2026-06-04 20:00', $page->getSelectedOccurrence()?->starts->format('Y-m-d H:i'));
    }

    public function test_responding_from_the_page_records_a_response_for_the_selected_date(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $user = $this->actingAsMember();

        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $second = Date::parse('2026-06-11 20:00:00');

        Livewire::withQueryParams(['occurrence' => $second->getTimestamp()])
            ->test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertSeeHtml(sprintf("respondToEvent(%d, %d, 'going', false)", $event->getKey(), $second->getTimestamp()))
            ->call('respondToEvent', $event->getKey(), $second->getTimestamp(), EventRegistrationStatus::GOING->value, false)
            ->assertNotified('You responded Going.')
            ->assertSee('You responded Going');

        $occurrence = EventOccurrence::query()->where('event_id', $event->getKey())->sole();

        $this->assertSame('2026-06-11 20:00', $occurrence->starts->format('Y-m-d H:i'));
        $this->assertDatabaseHas('events_registrations', ['event_id' => $event->getKey(), 'user_id' => $user->getKey(), 'event_occurrence_id' => $occurrence->getKey(), 'status' => EventRegistrationStatus::GOING->value]);
        $this->assertDatabaseMissing('events_registrations', ['event_id' => $event->getKey(), 'user_id' => $user->getKey(), 'event_occurrence_id' => null]);
    }

    public function test_responding_for_all_dates_records_a_series_response(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $user = $this->actingAsMember();

        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $first = Date::parse('2026-06-04 20:00:00');

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->set('wholeSeries', true)
            ->assertSeeHtml(sprintf("respondToEvent(%d, %d, 'tentative', true)", $event->getKey(), $first->getTimestamp()))
            ->call('respondToEvent', $event->getKey(), $first->getTimestamp(), EventRegistrationStatus::TENTATIVE->value, true)
            ->assertNotified('You responded Maybe.');

        $this->assertDatabaseHas('events_registrations', ['event_id' => $event->getKey(), 'user_id' => $user->getKey(), 'event_occurrence_id' => null, 'status' => EventRegistrationStatus::TENTATIVE->value]);
    }

    public function test_a_cancelled_date_shows_the_cancelled_state_and_hides_rsvp(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $this->actingAsMember();

        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $cancelled = Date::parse('2026-06-11 20:00:00');
        EventOccurrence::factory()->createQuietly(['event_id' => $event->getKey(), 'starts' => $cancelled, 'ends' => $cancelled->copy()->addHour(), 'cancelled_at' => now(), 'notes' => 'Range is closed for maintenance.']);

        Livewire::withQueryParams(['occurrence' => $cancelled->getTimestamp()])
            ->test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertSee('Cancelled')
            ->assertSee('This date has been cancelled.')
            ->assertSee('Range is closed for maintenance.')
            ->assertDontSeeHtml('respondToEvent(');
    }

    public function test_rsvp_is_hidden_when_registration_is_disabled(): void
    {
        $this->actingAsMember();

        $event = Event::factory()->createQuietly(['registration_enabled' => false, 'location' => null, 'starts' => now()->addDay(), 'ends' => now()->addDay()->addHour()]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertSee('RSVPs are not being collected for this event.')
            ->assertDontSeeHtml('respondToEvent(');
    }

    public function test_admin_actions_and_registrations_are_hidden_from_members(): void
    {
        $this->actingAsMember();

        $event = Event::factory()->createQuietly(['location' => null, 'url' => 'https://example.com/briefing']);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertActionVisible('url')
            ->assertActionHidden('edit')
            ->assertActionHidden('delete')
            ->assertActionHidden('takeAttendance')
            ->assertActionHidden('checkInLink');

        $this->assertFalse(RegistrationsRelationManager::canViewForRecord($event, ViewEvent::class));
    }

    public function test_admins_can_see_the_registrations_relation_manager(): void
    {
        $this->actingAsSuperAdmin();

        $event = Event::factory()->createQuietly(['location' => null, 'url' => null]);

        Livewire::test(ViewEvent::class, ['record' => $event->getKey()])
            ->assertActionHidden('url')
            ->assertActionVisible('edit')
            ->assertActionVisible('takeAttendance');

        $this->assertTrue(RegistrationsRelationManager::canViewForRecord($event, ViewEvent::class));
    }

    private function makeWeeklyEvent(CarbonInterface $start): Event
    {
        $event = Event::factory()->createQuietly([
            'repeats' => true,
            'all_day' => false,
            'location' => null,
            'starts' => $start,
            'ends' => $start->copy()->addHour(),
            'registration_enabled' => true,
            'registration_deadline' => null,
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

    private function actingAsSuperAdmin(): User
    {
        $user = User::factory()->createQuietly();
        $user->assignRole(Utils::getSuperAdminName());

        $this->actingAs($user);

        return $user;
    }

    private function actingAsMember(): User
    {
        $user = User::factory()->createQuietly();
        $user->givePermissionTo(Permission::findOrCreate('view_event'), Permission::findOrCreate('view_any_event'));

        $this->actingAs($user);

        return $user;
    }
}
