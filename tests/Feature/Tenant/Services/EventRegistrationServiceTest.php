<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Data\Events\OccurrenceData;
use App\Exceptions\EventRegistrationException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\EventRegistration;
use App\Models\Schedule;
use App\Models\User;
use App\Services\EventOccurrenceService;
use App\Services\EventRegistrationService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date;
use Tests\Feature\Tenant\TenantTestCase;

class EventRegistrationServiceTest extends TenantTestCase
{
    public function test_responding_to_a_one_time_event_is_series_wide(): void
    {
        $event = $this->makeOneTimeEvent(now()->addDay());
        $user = User::factory()->createQuietly();

        EventRegistrationService::respond(EventOccurrenceService::nearest($event), $user, EventRegistrationStatus::GOING);
        EventRegistrationService::respond(EventOccurrenceService::nearest($event), $user, EventRegistrationStatus::TENTATIVE);

        $this->assertSame(1, EventRegistration::query()->where('event_id', $event->getKey())->count());
        $this->assertSame(EventRegistrationStatus::TENTATIVE, EventRegistration::query()->where('event_id', $event->getKey())->sole()->status);
    }

    public function test_responding_to_one_date_overrides_only_that_date(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $user = User::factory()->createQuietly();
        $first = EventOccurrenceService::find($event, Date::parse('2026-06-04 20:00:00'));
        $second = EventOccurrenceService::find($event, Date::parse('2026-06-11 20:00:00'));

        EventRegistrationService::respond($first, $user, EventRegistrationStatus::GOING, wholeSeries: true);
        EventRegistrationService::respond($second, $user, EventRegistrationStatus::NOT_GOING);

        $this->assertSame(EventRegistrationStatus::GOING, EventRegistrationService::responseFor($first, $user));
        $this->assertSame(EventRegistrationStatus::NOT_GOING, EventRegistrationService::responseFor($this->fresh($second), $user));
    }

    public function test_a_series_response_clears_upcoming_per_date_responses_but_keeps_attendance(): void
    {
        $this->travelTo(Date::parse('2026-06-12 12:00:00'));
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $user = User::factory()->createQuietly();
        $past = EventOccurrenceService::persist(EventOccurrenceService::find($event, Date::parse('2026-06-11 20:00:00')));
        $upcoming = EventOccurrenceService::find($event, Date::parse('2026-06-18 20:00:00'));

        EventRegistration::query()->create(['event_id' => $event->getKey(), 'user_id' => $user->getKey(), 'event_occurrence_id' => $past->getKey(), 'status' => EventRegistrationStatus::ATTENDED]);
        EventRegistrationService::respond($upcoming, $user, EventRegistrationStatus::NOT_GOING);
        EventRegistrationService::respond($upcoming, $user, EventRegistrationStatus::GOING, wholeSeries: true);

        $this->assertSame(EventRegistrationStatus::GOING, EventRegistrationService::responseFor($this->fresh($upcoming), $user));
        $this->assertDatabaseHas('events_registrations', ['event_occurrence_id' => $past->getKey(), 'status' => EventRegistrationStatus::ATTENDED->value]);
    }

    public function test_responses_are_refused_when_registration_is_closed(): void
    {
        $user = User::factory()->createQuietly();

        $this->assertRefused(EventOccurrenceService::nearest($this->makeOneTimeEvent(now()->addDay(), ['registration_enabled' => false])), $user);
        $this->assertRefused(EventOccurrenceService::nearest($this->makeOneTimeEvent(now()->addDay(), ['registration_deadline' => now()->subHour()])), $user);
        $this->assertRefused(EventOccurrenceService::nearest($this->makeOneTimeEvent(now()->subDay())), $user);
    }

    public function test_responses_are_refused_for_a_cancelled_occurrence(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly(['starts' => Date::parse('2026-06-04 20:00:00'), 'ends' => Date::parse('2026-06-04 21:00:00')]);

        $this->assertRefused(EventOccurrenceService::find($event, Date::parse('2026-06-04 20:00:00')), User::factory()->createQuietly());
    }

    public function test_only_member_responses_are_accepted(): void
    {
        $this->expectException(EventRegistrationException::class);

        EventRegistrationService::respond(EventOccurrenceService::nearest($this->makeOneTimeEvent(now()->addDay())), User::factory()->createQuietly(), EventRegistrationStatus::ATTENDED);
    }

    public function test_summary_and_responses_group_members(): void
    {
        $occurrence = EventOccurrenceService::nearest($this->makeOneTimeEvent(now()->addDay()));
        [$going, $maybe, $out] = User::factory()->count(3)->createQuietly()->all();

        EventRegistrationService::respond($occurrence, $going, EventRegistrationStatus::GOING);
        EventRegistrationService::respond($occurrence, $maybe, EventRegistrationStatus::TENTATIVE);
        EventRegistrationService::respond($occurrence, $out, EventRegistrationStatus::NOT_GOING);

        $this->assertSame(['going' => 1, 'maybe' => 1, 'not_going' => 1], EventRegistrationService::summary($occurrence));
        $this->assertTrue(EventRegistrationService::responses($occurrence)->get(EventRegistrationStatus::GOING->value)->first()->getRelation('user')->is($going));
    }

    public function test_occurrence_row_renders_rsvp_controls_and_status(): void
    {
        $occurrence = EventOccurrenceService::nearest($this->makeOneTimeEvent(now()->addDay(), ['name' => 'Range Day']));

        $html = Blade::render('<x-events.occurrence-row :occurrence="$occurrence" :status="$status" />', [
            'occurrence' => $occurrence,
            'status' => EventRegistrationStatus::GOING,
        ]);

        $this->assertStringContainsString('Range Day', $html);
        $this->assertStringContainsString("respondToEvent({$occurrence->event->getKey()}, {$occurrence->starts->getTimestamp()}, 'tentative'", $html);
        $this->assertStringContainsString('aria-pressed="true"', $html);
    }

    private function assertRefused(OccurrenceData $occurrence, User $user): void
    {
        $this->assertFalse(EventRegistrationService::canRespond($occurrence));

        try {
            EventRegistrationService::respond($occurrence, $user, EventRegistrationStatus::GOING);
            $this->fail('The response should have been refused.');
        } catch (EventRegistrationException) {
            $this->assertDatabaseMissing('events_registrations', ['event_id' => $occurrence->event->getKey(), 'user_id' => $user->getKey()]);
        }
    }

    private function fresh(OccurrenceData $occurrence): OccurrenceData
    {
        return EventOccurrenceService::find($occurrence->event, $occurrence->starts);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeOneTimeEvent(mixed $starts, array $attributes = []): Event
    {
        return Event::factory()->createQuietly([
            'all_day' => false,
            'starts' => $starts,
            'ends' => $starts->copy()->addHour(),
            'registration_enabled' => true,
            'registration_deadline' => null,
            ...$attributes,
        ]);
    }

    private function makeWeeklyEvent(mixed $start): Event
    {
        $event = Event::factory()->createQuietly(['all_day' => false, 'repeats' => true, 'starts' => $start, 'ends' => $start->copy()->addHour(), 'registration_enabled' => true, 'registration_deadline' => null]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly(['start' => $start, 'duration' => 1, 'frequency' => ScheduleFrequency::WEEKLY, 'interval' => 1, 'end_type' => ScheduleEndType::NEVER]);

        return $event->refresh();
    }
}
