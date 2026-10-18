<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\EventRegistration;
use App\Models\User;
use App\Notifications\Tenant\EventOccurrenceCancelled;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EventCalendarService
{
    /**
     * Responses that put an occurrence on a member's own calendar.
     *
     * @var array<int, EventRegistrationStatus>
     */
    public const array MY_EVENT_RESPONSES = [
        EventRegistrationStatus::GOING,
        EventRegistrationStatus::TENTATIVE,
        EventRegistrationStatus::INTERESTED,
        EventRegistrationStatus::ATTENDED,
    ];

    /**
     * Responses whose members are told when an occurrence is cancelled.
     *
     * @var array<int, EventRegistrationStatus>
     */
    public const array NOTIFIED_RESPONSES = [
        EventRegistrationStatus::GOING,
        EventRegistrationStatus::TENTATIVE,
        EventRegistrationStatus::INTERESTED,
    ];

    /**
     * The occurrences shown on the calendar for a range, narrowed by the calendar filters.
     *
     * @param  array{calendars?: array<int, int|string>, mine?: bool, open?: bool}  $filters
     * @return Collection<int, OccurrenceData>
     */
    public static function occurrences(CarbonInterface $start, CarbonInterface $end, array $filters = [], ?User $user = null, bool $includeCancelled = true): Collection
    {
        $calendarIds = collect($filters['calendars'] ?? [])
            ->map(fn (int|string $id): int => (int) $id)
            ->filter()
            ->values();

        $mine = (bool) ($filters['mine'] ?? false);
        $open = (bool) ($filters['open'] ?? false);

        if ($mine && ! $user instanceof User) {
            return collect();
        }

        $query = Event::query()
            ->when($calendarIds->isNotEmpty(), fn (Builder $query): Builder => $query->whereIn('calendar_id', $calendarIds->all()))
            ->when($open, fn (Builder $query): Builder => $query
                ->where('registration_enabled', true)
                ->where(fn (Builder $query): Builder => $query->whereNull('registration_deadline')->orWhere('registration_deadline', '>', now())))
            ->when($mine && $user instanceof User, fn (Builder $query): Builder => $query->whereIn('id', EventRegistration::query()
                ->select('event_id')
                ->where('user_id', $user?->getKey())));

        $occurrences = EventOccurrenceService::between($start, $end, $query)
            ->unless($includeCancelled, fn (Collection $occurrences): Collection => $occurrences->reject(fn (OccurrenceData $occurrence): bool => $occurrence->isCancelled()))
            ->when($open, fn (Collection $occurrences): Collection => $occurrences->filter(fn (OccurrenceData $occurrence): bool => EventRegistrationService::canRespond($occurrence)));

        if ($mine && $user instanceof User) {
            $responses = static::responses($occurrences, $user);

            $occurrences = $occurrences->filter(fn (OccurrenceData $occurrence): bool => in_array($responses->get($occurrence->key()), static::MY_EVENT_RESPONSES, true));
        }

        return $occurrences->values();
    }

    /**
     * The member's effective response to each occurrence they have answered, keyed by occurrence
     * key. A response to the occurrence itself takes precedence over the member's series-wide one.
     *
     * @param  Collection<int, OccurrenceData>  $occurrences
     * @return Collection<string, EventRegistrationStatus>
     */
    public static function responses(Collection $occurrences, ?User $user): Collection
    {
        $registrations = $user instanceof User && $occurrences->isNotEmpty()
            ? EventRegistration::query()
                ->where('user_id', $user->getKey())
                ->whereIn('event_id', $occurrences->map(fn (OccurrenceData $occurrence): int => $occurrence->event->getKey())->unique()->values()->all())
                ->get()
            : new EloquentCollection;

        return $occurrences
            ->mapWithKeys(fn (OccurrenceData $occurrence): array => [
                $occurrence->key() => static::effectiveResponse($occurrence, $registrations),
            ])
            ->filter(fn (?EventRegistrationStatus $status): bool => $status instanceof EventRegistrationStatus);
    }

    /**
     * Whether a single date of the event can be cancelled or restored on its own.
     */
    public static function canCancelDate(OccurrenceData $occurrence): bool
    {
        return $occurrence->event->repeats;
    }

    /**
     * Cancel a single date of a repeating event and tell members who planned to attend.
     */
    public static function cancel(OccurrenceData $occurrence, ?string $reason = null): EventOccurrence
    {
        $record = DB::transaction(function () use ($occurrence, $reason): EventOccurrence {
            $record = EventOccurrenceService::persist($occurrence);

            $record->fill([
                'cancelled_at' => now(),
                'notes' => filled($reason) ? $reason : null,
            ])->save();

            return $record;
        });

        $recipients = EventAttendanceService::registrationsFor($occurrence)
            ->filter(fn (EventRegistration $registration): bool => in_array($registration->status, static::NOTIFIED_RESPONSES, true))
            ->keys();

        $users = User::query()->whereKey($recipients->all())->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new EventOccurrenceCancelled($record));
        }

        return $record;
    }

    /**
     * Put a cancelled date back on the calendar.
     */
    public static function restore(OccurrenceData $occurrence): EventOccurrence
    {
        $record = EventOccurrenceService::persist($occurrence);

        $record->fill([
            'cancelled_at' => null,
            'notes' => null,
        ])->save();

        return $record;
    }

    /**
     * @param  EloquentCollection<int, EventRegistration>  $registrations
     */
    protected static function effectiveResponse(OccurrenceData $occurrence, EloquentCollection $registrations): ?EventRegistrationStatus
    {
        $forEvent = $registrations->where('event_id', $occurrence->event->getKey());
        $occurrenceId = $occurrence->event->repeats ? $occurrence->occurrence?->getKey() : null;

        $registration = ($occurrenceId !== null ? $forEvent->firstWhere('event_occurrence_id', $occurrenceId) : null)
            ?? $forEvent->first(fn (EventRegistration $registration): bool => $registration->event_occurrence_id === null);

        return $registration?->status;
    }
}
