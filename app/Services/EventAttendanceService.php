<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\EventCheckInException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\TrainingRecord;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
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
     * @return array{0: CarbonInterface, 1: CarbonInterface}
     */
    public static function checkInWindow(Event $event, ?CarbonInterface $at = null): array
    {
        $at ??= now();

        return static::occurrences($event)
            ->map(fn (array $occurrence): array => [
                $occurrence[0]->copy()->subMinutes(static::CHECK_IN_GRACE_MINUTES),
                $occurrence[1]->copy()->addMinutes(static::CHECK_IN_GRACE_MINUTES),
            ])
            ->sortBy(fn (array $window): float => match (true) {
                $at->between($window[0], $window[1]) => 0,
                default => min(abs($at->diffInSeconds($window[0])), abs($at->diffInSeconds($window[1]))),
            })
            ->first();
    }

    public static function isWithinCheckInWindow(Event $event, ?CarbonInterface $at = null): bool
    {
        $at ??= now();

        [$opens, $closes] = static::checkInWindow($event, $at);

        return $at->between($opens, $closes);
    }

    public static function checkInUrl(Event $event): string
    {
        return URL::to(URL::signedRoute('app.events.check-in', ['event' => $event->getKey()], absolute: false));
    }

    /**
     * @throws EventCheckInException
     */
    public static function checkIn(Event $event, User $user): EventRegistration
    {
        throw_unless(static::isWithinCheckInWindow($event), EventCheckInException::outsideWindow());

        $isRegistered = $event->registrations()->whereKey($user->getKey())->exists();

        throw_if(! $isRegistered && ! $event->registration_enabled, EventCheckInException::registrationDisabled());

        static::markAttendance($event, $user, EventRegistrationStatus::ATTENDED, now());

        return static::registrationFor($event, $user);
    }

    /**
     * @param  array<int, int>  $attendedUserIds
     * @param  array<int, int>  $noShowUserIds
     */
    public static function recordAttendance(Event $event, array $attendedUserIds, array $noShowUserIds = []): void
    {
        DB::transaction(function () use ($event, $attendedUserIds, $noShowUserIds): void {
            User::query()->whereKey($attendedUserIds)->get()->each(fn (User $user) => static::markAttendance($event, $user, EventRegistrationStatus::ATTENDED));
            User::query()->whereKey(array_diff($noShowUserIds, $attendedUserIds))->get()->each(fn (User $user) => static::markAttendance($event, $user, EventRegistrationStatus::NO_SHOW));
        });
    }

    /**
     * Create a training record for an attended registration when the event is configured to do so.
     * Returns null when no record is needed or one already exists for the user and event.
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
            ->exists();

        if ($exists) {
            return null;
        }

        return TrainingRecord::query()->create([
            'user_id' => $registration->user_id,
            'event_id' => $event->getKey(),
            'text' => sprintf('Attended %s.', $event->name),
        ]);
    }

    protected static function markAttendance(Event $event, User $user, EventRegistrationStatus $status, ?CarbonInterface $checkedInAt = null): void
    {
        $attributes = collect(['status' => $status, 'checked_in_at' => $checkedInAt])
            ->filter(fn (mixed $value): bool => filled($value))
            ->all();

        if ($event->registrations()->whereKey($user->getKey())->exists()) {
            $event->registrations()->updateExistingPivot($user->getKey(), $attributes);

            return;
        }

        $event->registrations()->attach($user->getKey(), $attributes);
    }

    protected static function registrationFor(Event $event, User $user): EventRegistration
    {
        /** @var EventRegistration */
        return EventRegistration::query()
            ->where('event_id', $event->getKey())
            ->where('user_id', $user->getKey())
            ->firstOrFail();
    }

    /**
     * @return Collection<int, array{0: Carbon, 1: Carbon}>
     */
    protected static function occurrences(Event $event): Collection
    {
        $schedule = $event->schedule;

        if (blank($schedule)) {
            $ends = $event->ends ?? $event->starts;

            return collect([[$event->starts, $event->all_day ? $ends->copy()->endOfDay() : $ends]]);
        }

        return collect([$schedule->last_occurrence, $schedule->next_occurrence])
            ->filter()
            ->whenEmpty(fn (Collection $collection): Collection => $collection->push($schedule->start))
            ->map(fn (Carbon $start): array => [$start, $start->copy()->addHours($schedule->duration)])
            ->values();
    }
}
