<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Models\Enums\ScheduleEndType;
use App\Models\Event;
use App\Models\EventOccurrence;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

class EventOccurrenceService
{
    /**
     * Every occurrence that overlaps the given range, across one-time and repeating events.
     *
     * @param  Builder<Event>|null  $query
     * @return Collection<int, OccurrenceData>
     */
    public static function between(CarbonInterface $start, CarbonInterface $end, ?Builder $query = null): Collection
    {
        $from = $start->copy()->subDay();
        $to = $end->copy()->addDay();

        return ($query ?? Event::query())
            ->with(['calendar', 'schedule'])
            ->where(fn (Builder $query): Builder => $query
                ->where(fn (Builder $query): Builder => $query
                    ->where('repeats', false)
                    ->where('starts', '<=', $to)
                    ->whereRaw('COALESCE(ends, starts) >= ?', [$from]))
                ->orWhere(fn (Builder $query): Builder => $query
                    ->where('repeats', true)
                    ->whereHas('schedule', fn (Builder $query): Builder => $query
                        ->where('start', '<=', $to)
                        ->where(fn (Builder $query): Builder => $query
                            ->whereNull('until')
                            ->orWhereNot('end_type', ScheduleEndType::ON)
                            ->orWhereNull('end_type')
                            ->orWhere('until', '>=', $from)))))
            ->get()
            ->flatMap(fn (Event $event): Collection => static::forEvent($event, $start, $end))
            ->sortBy(fn (OccurrenceData $occurrence): int => $occurrence->starts->getTimestamp())
            ->values();
    }

    /**
     * @return Collection<int, OccurrenceData>
     */
    public static function forEvent(Event $event, CarbonInterface $start, CarbonInterface $end): Collection
    {
        $occurrences = static::expand($event, $start, $end)
            ->filter(fn (OccurrenceData $occurrence): bool => $occurrence->starts->lessThanOrEqualTo($end) && $occurrence->ends->greaterThanOrEqualTo($start))
            ->values();

        return static::attachPersisted($event, $occurrences);
    }

    /**
     * The occurrence that is happening at, or is closest to, the given time.
     */
    public static function nearest(Event $event, ?CarbonInterface $at = null): ?OccurrenceData
    {
        $at ??= now();

        if (! $event->repeats || blank($event->schedule)) {
            return static::attachPersisted($event, collect([static::oneTime($event)]))->first();
        }

        $candidates = collect([
            ScheduleService::previousOccurrence($event->schedule, $at),
            ScheduleService::followingOccurrence($event->schedule, $at),
        ])
            ->filter()
            ->map(fn (Carbon $starts): OccurrenceData => static::repeating($event, $starts));

        return static::attachPersisted($event, $candidates)
            ->sortBy(fn (OccurrenceData $occurrence): float => $at->between($occurrence->starts, $occurrence->ends)
                ? 0
                : min(abs($at->diffInSeconds($occurrence->starts)), abs($at->diffInSeconds($occurrence->ends))))
            ->first();
    }

    /**
     * The occurrences surrounding the given time: up to $before occurrences starting at or before
     * it followed by up to $after starting after it. One-time events return their only occurrence.
     *
     * @return Collection<int, OccurrenceData>
     */
    public static function around(Event $event, CarbonInterface $at, int $before = 4, int $after = 8): Collection
    {
        if (! $event->repeats || blank($event->schedule)) {
            return static::attachPersisted($event, collect([static::oneTime($event)]));
        }

        return static::attachPersisted($event, collect(ScheduleService::occurrencesAround($event->schedule, $at, $before, $after))
            ->map(fn (Carbon $starts): OccurrenceData => static::repeating($event, $starts)));
    }

    /**
     * The occurrence of the event that starts exactly at the given time.
     */
    public static function find(Event $event, CarbonInterface $starts): ?OccurrenceData
    {
        return static::forEvent($event, $starts, $starts)
            ->first(fn (OccurrenceData $occurrence): bool => $occurrence->starts->equalTo($starts));
    }

    /**
     * Find or create the stored row for an occurrence.
     */
    public static function persist(OccurrenceData $occurrence): EventOccurrence
    {
        return $occurrence->occurrence ??= EventOccurrence::query()->firstOrCreate([
            'event_id' => $occurrence->event->getKey(),
            'starts' => $occurrence->starts,
        ], [
            'ends' => $occurrence->ends,
        ]);
    }

    /**
     * The occurrence id registrations for this occurrence are stored under. One-time events keep
     * using series-wide registrations, so their id is null.
     */
    public static function registrationOccurrenceId(OccurrenceData $occurrence): ?int
    {
        if (! $occurrence->event->repeats) {
            return null;
        }

        return static::persist($occurrence)->getKey();
    }

    /**
     * @return Collection<int, OccurrenceData>
     */
    protected static function expand(Event $event, CarbonInterface $start, CarbonInterface $end): Collection
    {
        if (! $event->repeats) {
            return collect([static::oneTime($event)]);
        }

        if (blank($event->schedule)) {
            return collect();
        }

        $lookback = $event->all_day
            ? (static::allDaySpanInDays((int) $event->schedule->duration) + 1) * 24
            : (int) $event->schedule->duration;

        return collect(ScheduleService::occurrenceBetween($event->schedule, $start->copy()->subHours($lookback), $end) ?? [])
            ->map(fn (Carbon $starts): OccurrenceData => static::repeating($event, $starts));
    }

    /**
     * All-day events store bare calendar dates, so their occurrence spans those whole days in the
     * organization's timezone.
     */
    protected static function oneTime(Event $event): OccurrenceData
    {
        if ($event->all_day) {
            $timezone = ScheduleService::timezone();

            return new OccurrenceData(
                event: $event,
                starts: Date::parse($event->starts->toDateString(), $timezone)->startOfDay()->setTimezone(config('app.timezone')),
                ends: Date::parse(($event->ends ?? $event->starts)->toDateString(), $timezone)->endOfDay()->setTimezone(config('app.timezone')),
                allDay: true,
            );
        }

        return new OccurrenceData(
            event: $event,
            starts: $event->starts->copy(),
            ends: ($event->ends ?? $event->starts)->copy(),
            allDay: false,
        );
    }

    /**
     * All-day occurrences run through the end of their last calendar day in the organization's timezone.
     */
    protected static function repeating(Event $event, Carbon $starts): OccurrenceData
    {
        $duration = (int) $event->schedule->duration;

        return new OccurrenceData(
            event: $event,
            starts: $starts,
            ends: $event->all_day
                ? $starts->copy()->setTimezone(ScheduleService::timezone())->addDays(static::allDaySpanInDays($duration))->endOfDay()->setTimezone(config('app.timezone'))
                : $starts->copy()->addHours($duration),
            allDay: $event->all_day,
        );
    }

    /**
     * The number of extra calendar days an all-day occurrence spans. Durations are whole hours
     * between the start and end dates, so a single-day event is 0 (or the form default of 1).
     */
    protected static function allDaySpanInDays(int $duration): int
    {
        return max(0, (int) round($duration / 24));
    }

    /**
     * @param  Collection<int, OccurrenceData>  $occurrences
     * @return Collection<int, OccurrenceData>
     */
    protected static function attachPersisted(Event $event, Collection $occurrences): Collection
    {
        if ($occurrences->isEmpty()) {
            return $occurrences;
        }

        $persisted = EventOccurrence::query()
            ->where('event_id', $event->getKey())
            ->whereIn('starts', $occurrences->map(fn (OccurrenceData $occurrence): Carbon => $occurrence->starts)->all())
            ->get()
            ->keyBy(fn (EventOccurrence $occurrence): int => $occurrence->starts->getTimestamp());

        return $occurrences->each(function (OccurrenceData $occurrence) use ($persisted): void {
            $occurrence->occurrence = $persisted->get($occurrence->starts->getTimestamp());
        });
    }
}
