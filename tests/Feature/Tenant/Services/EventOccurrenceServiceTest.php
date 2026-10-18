<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Data\Events\OccurrenceData;
use App\Models\Enums\ScheduleEndType;
use App\Models\Enums\ScheduleFrequency;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\Schedule;
use App\Services\EventOccurrenceService;
use App\Settings\OrganizationSettings;
use Illuminate\Support\Facades\Date;
use Tests\Feature\Tenant\TenantTestCase;

class EventOccurrenceServiceTest extends TenantTestCase
{
    public function test_between_expands_repeating_events_alongside_one_time_events(): void
    {
        $this->travelTo(Date::parse('2026-06-01 12:00:00'));

        $weekly = $this->makeWeeklyEvent(Date::parse('2026-05-07 20:00:00'));
        $oneTime = $this->makeOneTimeEvent(Date::parse('2026-06-10 18:00:00'));
        $this->makeOneTimeEvent(Date::parse('2026-08-01 18:00:00'));

        $occurrences = EventOccurrenceService::between(Date::parse('2026-06-01'), Date::parse('2026-06-30 23:59:59'));

        $this->assertSame(
            ['2026-06-04 20:00', '2026-06-10 18:00', '2026-06-11 20:00', '2026-06-18 20:00', '2026-06-25 20:00'],
            $occurrences->map(fn (OccurrenceData $occurrence): string => $occurrence->starts->format('Y-m-d H:i'))->all(),
        );
        $this->assertSame([$weekly->getKey(), $oneTime->getKey()], $occurrences->map(fn (OccurrenceData $occurrence): int => $occurrence->event->getKey())->unique()->sort()->values()->all());
    }

    public function test_between_skips_repeating_events_that_ended_before_the_range(): void
    {
        $this->makeWeeklyEvent(Date::parse('2026-01-01 20:00:00'), [
            'end_type' => ScheduleEndType::ON,
            'until' => Date::parse('2026-02-01'),
        ]);

        $this->assertTrue(EventOccurrenceService::between(Date::parse('2026-06-01'), Date::parse('2026-06-30'))->isEmpty());
    }

    public function test_between_includes_an_occurrence_already_in_progress_at_the_start_of_the_range(): void
    {
        $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'), ['duration' => 3]);

        $occurrences = EventOccurrenceService::between(Date::parse('2026-06-04 21:00:00'), Date::parse('2026-06-04 23:00:00'));

        $this->assertCount(1, $occurrences);
        $this->assertSame('2026-06-04 20:00', $occurrences->first()->starts->format('Y-m-d H:i'));
    }

    public function test_repeating_events_keep_their_local_time_across_daylight_saving(): void
    {
        OrganizationSettings::fake(['timezone' => 'America/New_York']);

        // 8pm Eastern on a Thursday in winter (EST, UTC-5) is 1am Friday in UTC.
        $event = $this->makeWeeklyEvent(Date::parse('2026-01-02 01:00:00'));

        $occurrences = EventOccurrenceService::forEvent($event, Date::parse('2026-03-01'), Date::parse('2026-03-20'));

        $this->assertSame(
            ['2026-03-06 01:00', '2026-03-13 00:00', '2026-03-20 00:00'],
            $occurrences->map(fn (OccurrenceData $occurrence): string => $occurrence->starts->format('Y-m-d H:i'))->all(),
        );
        $occurrences->each(fn (OccurrenceData $occurrence) => $this->assertSame('Thu 20:00', $occurrence->starts->copy()->setTimezone('America/New_York')->format('D H:i')));
    }

    public function test_nearest_prefers_the_occurrence_in_progress(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'), ['duration' => 2]);

        $occurrence = EventOccurrenceService::nearest($event, Date::parse('2026-06-11 21:00:00'));

        $this->assertSame('2026-06-11 20:00', $occurrence?->starts->format('Y-m-d H:i'));
    }

    public function test_nearest_picks_the_closest_occurrence_between_two(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'), ['duration' => 2]);

        $this->assertSame('2026-06-04 20:00', EventOccurrenceService::nearest($event, Date::parse('2026-06-05 09:00:00'))?->starts->format('Y-m-d H:i'));
        $this->assertSame('2026-06-11 20:00', EventOccurrenceService::nearest($event, Date::parse('2026-06-10 09:00:00'))?->starts->format('Y-m-d H:i'));
    }

    public function test_nearest_returns_the_single_occurrence_of_a_one_time_event(): void
    {
        $event = $this->makeOneTimeEvent(Date::parse('2026-06-10 18:00:00'));

        $occurrence = EventOccurrenceService::nearest($event, Date::parse('2027-01-01'));

        $this->assertTrue($occurrence?->starts->equalTo($event->starts));
        $this->assertTrue($occurrence->ends->equalTo($event->ends));
    }

    public function test_persist_is_idempotent_and_attached_on_later_lookups(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        $starts = Date::parse('2026-06-11 20:00:00');

        $first = EventOccurrenceService::persist(EventOccurrenceService::find($event, $starts));
        $second = EventOccurrenceService::persist(EventOccurrenceService::find($event, $starts));

        $this->assertTrue($first->is($second));
        $this->assertSame(1, EventOccurrence::query()->where('event_id', $event->getKey())->count());
        $this->assertTrue(EventOccurrenceService::find($event, $starts)?->occurrence?->is($first));
    }

    public function test_cancelled_occurrences_are_flagged(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly([
            'starts' => Date::parse('2026-06-11 20:00:00'),
            'ends' => Date::parse('2026-06-11 21:00:00'),
        ]);

        $occurrences = EventOccurrenceService::forEvent($event, Date::parse('2026-06-01'), Date::parse('2026-06-15'));

        $this->assertSame([false, true], $occurrences->map(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled())->all());
    }

    public function test_registrations_for_one_time_events_are_not_scoped_to_an_occurrence(): void
    {
        $oneTime = $this->makeOneTimeEvent(Date::parse('2026-06-10 18:00:00'));
        $weekly = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));

        $this->assertNull(EventOccurrenceService::registrationOccurrenceId(EventOccurrenceService::nearest($oneTime)));
        $this->assertSame(0, EventOccurrence::query()->where('event_id', $oneTime->getKey())->count());
        $this->assertNotNull(EventOccurrenceService::registrationOccurrenceId(EventOccurrenceService::nearest($weekly)));
    }

    public function test_around_lists_dates_before_and_after_the_given_time(): void
    {
        $event = $this->makeWeeklyEvent(Date::parse('2026-06-04 20:00:00'));
        EventOccurrence::factory()->for($event)->cancelled()->createQuietly([
            'starts' => Date::parse('2026-06-25 20:00:00'),
            'ends' => Date::parse('2026-06-25 21:00:00'),
        ]);

        $occurrences = EventOccurrenceService::around($event, Date::parse('2026-06-18 20:00:00'), 2, 2);

        $this->assertSame(
            ['2026-06-11 20:00', '2026-06-18 20:00', '2026-06-25 20:00', '2026-07-02 20:00'],
            $occurrences->map(fn (OccurrenceData $occurrence): string => $occurrence->starts->format('Y-m-d H:i'))->all(),
        );
        $this->assertSame([false, false, true, false], $occurrences->map(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled())->all());
    }

    public function test_around_returns_the_single_occurrence_of_a_one_time_event(): void
    {
        $event = $this->makeOneTimeEvent(Date::parse('2026-06-10 18:00:00'));

        $occurrences = EventOccurrenceService::around($event, Date::parse('2027-01-01'));

        $this->assertCount(1, $occurrences);
        $this->assertTrue($occurrences->first()?->starts->equalTo($event->starts));
    }

    public function test_between_ignores_a_stale_until_when_the_schedule_does_not_end_on_a_date(): void
    {
        $this->makeWeeklyEvent(Date::parse('2026-01-01 20:00:00'), [
            'end_type' => ScheduleEndType::NEVER,
            'until' => Date::parse('2026-02-01'),
        ]);

        $this->assertCount(4, EventOccurrenceService::between(Date::parse('2026-06-01'), Date::parse('2026-06-30')));
    }

    public function test_one_time_all_day_events_span_the_whole_day_in_the_organization_timezone(): void
    {
        OrganizationSettings::fake(['timezone' => 'America/Los_Angeles']);

        $event = Event::factory()->createQuietly([
            'all_day' => true,
            'repeats' => false,
            'starts' => Date::parse('2026-10-17'),
            'ends' => Date::parse('2026-10-17'),
        ])->refresh();

        $occurrence = EventOccurrenceService::nearest($event);

        $this->assertSame('2026-10-17 07:00:00', $occurrence?->starts->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-18 06:59:59', $occurrence->ends->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-17', $occurrence->localStarts()->toDateString());
        $this->assertSame('2026-10-17', $occurrence->localEnds()->toDateString());

        $this->travelTo(Date::parse('2026-10-16 17:30:00', 'America/Los_Angeles'));
        $this->assertFalse($occurrence->isHappeningNow());

        $this->travelTo(Date::parse('2026-10-17 17:00:00', 'America/Los_Angeles'));
        $this->assertTrue($occurrence->isHappeningNow());
        $this->assertFalse($occurrence->hasEnded());

        $this->travelTo(Date::parse('2026-10-18 00:00:01', 'America/Los_Angeles'));
        $this->assertTrue($occurrence->hasEnded());
    }

    public function test_repeating_all_day_events_end_at_the_end_of_their_last_day_in_the_organization_timezone(): void
    {
        OrganizationSettings::fake(['timezone' => 'America/Los_Angeles']);

        $start = Date::parse('2026-10-17 00:00:00', 'America/Los_Angeles')->utc();
        $event = Event::factory()->createQuietly(['all_day' => true, 'repeats' => true, 'starts' => $start, 'ends' => $start]);
        Schedule::factory()->for($event, 'repeatable')->createQuietly([
            'start' => $start,
            'duration' => 1,
            'frequency' => ScheduleFrequency::WEEKLY,
            'interval' => 1,
            'end_type' => ScheduleEndType::NEVER,
        ]);
        $event->refresh();

        $occurrences = EventOccurrenceService::forEvent($event, Date::parse('2026-10-24 12:00:00'), Date::parse('2026-10-24 12:00:00'));

        $this->assertCount(1, $occurrences);
        $this->assertSame('2026-10-24 07:00:00', $occurrences->first()->starts->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-25 06:59:59', $occurrences->first()->ends->format('Y-m-d H:i:s'));

        $event->schedule->forceFill(['duration' => 24])->saveQuietly();

        $this->assertSame('2026-10-26 06:59:59', EventOccurrenceService::nearest($event->refresh(), Date::parse('2026-10-24 12:00:00'))?->ends->format('Y-m-d H:i:s'));
    }

    /**
     * @param  array<string, mixed>  $schedule
     */
    private function makeWeeklyEvent(mixed $start, array $schedule = []): Event
    {
        $event = Event::factory()->createQuietly([
            'all_day' => false,
            'repeats' => true,
            'starts' => $start,
            'ends' => $start->copy()->addHour(),
        ]);

        Schedule::factory()->for($event, 'repeatable')->createQuietly([
            'start' => $start,
            'duration' => 1,
            'frequency' => ScheduleFrequency::WEEKLY,
            'interval' => 1,
            'end_type' => ScheduleEndType::NEVER,
            ...$schedule,
        ]);

        return $event->refresh();
    }

    private function makeOneTimeEvent(mixed $start): Event
    {
        return Event::factory()->createQuietly([
            'all_day' => false,
            'repeats' => false,
            'starts' => $start,
            'ends' => $start->copy()->addHour(),
        ]);
    }
}
