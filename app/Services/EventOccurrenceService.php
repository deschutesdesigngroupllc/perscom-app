<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Models\Event;
use App\Models\EventOccurrence;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
                        ->where(fn (Builder $query): Builder => $query->whereNull('until')->orWhere('until', '>=', $from)))))
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

        return collect(ScheduleService::occurrenceBetween($event->schedule, $start->copy()->subHours($event->schedule->duration), $end) ?? [])
            ->map(fn (Carbon $starts): OccurrenceData => static::repeating($event, $starts));
    }

    protected static function oneTime(Event $event): OccurrenceData
    {
        $starts = $event->starts->copy();
        $ends = ($event->ends ?? $event->starts)->copy();

        return new OccurrenceData(
            event: $event,
            starts: $starts,
            ends: $event->all_day ? $ends->endOfDay() : $ends,
            allDay: $event->all_day,
        );
    }

    protected static function repeating(Event $event, Carbon $starts): OccurrenceData
    {
        $duration = (int) $event->schedule->duration;

        return new OccurrenceData(
            event: $event,
            starts: $starts,
            ends: $duration > 0 || ! $event->all_day ? $starts->copy()->addHours($duration) : $starts->copy()->endOfDay(),
            allDay: $event->all_day,
        );
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
