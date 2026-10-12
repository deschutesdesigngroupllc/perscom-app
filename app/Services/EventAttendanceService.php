<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Exceptions\EventCheckInException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventOccurrence;
use App\Models\EventRegistration;
use App\Models\TrainingRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class EventAttendanceService
{
    /**
     * The number of minutes before an occurrence starts and after it ends that check-in is open.
     */
    public const int CHECK_IN_GRACE_MINUTES = 60;

    /**
     * Resolve the check-in window for the occurrence of the event that is closest to the given time.
     *
     * @return array{0: CarbonInterface, 1: CarbonInterface}|null
     */
    public static function checkInWindow(Event $event, ?CarbonInterface $at = null): ?array
    {
        $occurrence = EventOccurrenceService::nearest($event, $at ?? now());

        return $occurrence instanceof OccurrenceData ? static::windowFor($occurrence) : null;
    }

    /**
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public static function windowFor(OccurrenceData $occurrence): array
    {
        return [
            $occurrence->starts->copy()->subMinutes(static::CHECK_IN_GRACE_MINUTES),
            $occurrence->ends->copy()->addMinutes(static::CHECK_IN_GRACE_MINUTES),
        ];
    }

    public static function isWithinCheckInWindow(Event $event, ?CarbonInterface $at = null): bool
    {
        $at ??= now();

        $occurrence = EventOccurrenceService::nearest($event, $at);

        if (! $occurrence instanceof OccurrenceData || $occurrence->isCancelled()) {
            return false;
        }

        [$opens, $closes] = static::windowFor($occurrence);

        return $at->between($opens, $closes);
    }

    public static function checkInUrl(Event $event): string
    {
        return URL::to(URL::signedRoute('app.events.check-in', ['event' => $event->getKey()], absolute: false));
    }

    /**
     * Check the user in to the occurrence that is currently open for check-in.
     *
     * @throws EventCheckInException
     */
    public static function checkIn(Event $event, User $user): EventRegistration
    {
        $occurrence = EventOccurrenceService::nearest($event, now());

        throw_if($occurrence?->isCancelled(), EventCheckInException::cancelled());
        throw_unless($occurrence instanceof OccurrenceData && static::isWithinCheckInWindow($event), EventCheckInException::outsideWindow());

        $isRegistered = EventRegistration::query()
            ->where('event_id', $event->getKey())
            ->where('user_id', $user->getKey())
            ->exists();

        throw_if(! $isRegistered && ! $event->registration_enabled, EventCheckInException::registrationDisabled());

        return static::markAttendance($occurrence, $user->getKey(), EventRegistrationStatus::ATTENDED, now());
    }

    /**
     * Record attendance for an occurrence. Defaults to the occurrence closest to now.
     *
     * @param  array<int, int>  $attendedUserIds
     * @param  array<int, int>  $noShowUserIds
     */
    public static function recordAttendance(Event $event, array $attendedUserIds, array $noShowUserIds = [], ?OccurrenceData $occurrence = null): void
    {
        $occurrence ??= EventOccurrenceService::nearest($event, now());

        if (! $occurrence instanceof OccurrenceData) {
            return;
        }

        DB::transaction(function () use ($occurrence, $attendedUserIds, $noShowUserIds): void {
            User::query()->whereKey($attendedUserIds)->pluck('id')->each(fn (int $userId): EventRegistration => static::markAttendance($occurrence, $userId, EventRegistrationStatus::ATTENDED));
            User::query()->whereKey(array_diff($noShowUserIds, $attendedUserIds))->pluck('id')->each(fn (int $userId): EventRegistration => static::markAttendance($occurrence, $userId, EventRegistrationStatus::NO_SHOW));
        });
    }

    /**
     * The registrations that apply to an occurrence, keyed by user id. A registration made for the
     * occurrence itself takes precedence over the member's series-wide registration.
     *
     * @return Collection<int, EventRegistration>
     */
    public static function registrationsFor(OccurrenceData $occurrence): Collection
    {
        $occurrenceId = $occurrence->event->repeats ? $occurrence->occurrence?->getKey() : null;

        return EventRegistration::query()
            ->where('event_id', $occurrence->event->getKey())
            ->where(fn (Builder $query): Builder => $query
                ->whereNull('event_occurrence_id')
                ->when($occurrenceId, fn (Builder $query, int $id): Builder => $query->orWhere('event_occurrence_id', $id)))
            ->get()
            ->sortBy(fn (EventRegistration $registration): int => $registration->event_occurrence_id === null ? 0 : 1)
            ->keyBy('user_id');
    }

    public static function statusFor(OccurrenceData $occurrence, User $user): ?EventRegistrationStatus
    {
        return static::registrationsFor($occurrence)->get($user->getKey())?->status;
    }

    /**
     * Create a training record for an attended registration when the event is configured to do so.
     * Returns null when no record is needed or one already exists for the user and occurrence.
     */
    public static function createTrainingRecord(EventRegistration $registration): ?TrainingRecord
    {
        $event = $registration->event;

        if (! $event instanceof Event || ! $event->creates_training_record || $registration->status !== EventRegistrationStatus::ATTENDED) {
            return null;
        }

        $exists = TrainingRecord::query()
            ->where('user_id', $registration->user_id)
            ->where('event_id', $event->getKey())
            ->where('event_occurrence_id', $registration->event_occurrence_id)
            ->exists();

        if ($exists) {
            return null;
        }

        $occurrence = $registration->occurrence;

        return TrainingRecord::query()->create([
            'user_id' => $registration->user_id,
            'event_id' => $event->getKey(),
            'event_occurrence_id' => $occurrence?->getKey(),
            'text' => $occurrence instanceof EventOccurrence
                ? sprintf('Attended %s on %s.', $event->name, $occurrence->starts->copy()->setTimezone(ScheduleService::timezone())->toFormattedDayDateString())
                : sprintf('Attended %s.', $event->name),
        ]);
    }

    protected static function markAttendance(OccurrenceData $occurrence, int $userId, EventRegistrationStatus $status, ?CarbonInterface $checkedInAt = null): EventRegistration
    {
        /** @var EventRegistration $registration */
        $registration = EventRegistration::query()->firstOrNew([
            'event_id' => $occurrence->event->getKey(),
            'user_id' => $userId,
            'event_occurrence_id' => EventOccurrenceService::registrationOccurrenceId($occurrence),
        ]);

        $registration->fill(collect(['status' => $status, 'checked_in_at' => $checkedInAt])
            ->filter(fn (mixed $value): bool => filled($value))
            ->all());

        $registration->save();

        return $registration;
    }
}
