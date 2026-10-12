<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Events\OccurrenceData;
use App\Exceptions\EventRegistrationException;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventRegistrationService
{
    /**
     * Whether members can still RSVP to the occurrence.
     */
    public static function canRespond(OccurrenceData $occurrence): bool
    {
        return static::closedReason($occurrence) === null;
    }

    /**
     * Record a member's RSVP. A response to a single occurrence of a repeating event overrides the
     * member's series response for that date only; a series response replaces the member's
     * outstanding per-date responses.
     *
     * @throws EventRegistrationException
     */
    public static function respond(OccurrenceData $occurrence, User $user, EventRegistrationStatus $status, bool $wholeSeries = false): EventRegistration
    {
        throw_unless($status->isMemberResponse(), EventRegistrationException::invalidResponse());

        if (($reason = static::closedReason($occurrence)) instanceof EventRegistrationException) {
            throw $reason;
        }

        $event = $occurrence->event;
        $seriesWide = $wholeSeries || ! $event->repeats;

        return DB::transaction(function () use ($occurrence, $user, $status, $event, $seriesWide): EventRegistration {
            /** @var EventRegistration $registration */
            $registration = EventRegistration::query()->firstOrNew([
                'event_id' => $event->getKey(),
                'user_id' => $user->getKey(),
                'event_occurrence_id' => $seriesWide ? null : EventOccurrenceService::registrationOccurrenceId($occurrence),
            ]);

            $registration->fill(['status' => $status])->save();

            if ($seriesWide && $event->repeats) {
                EventRegistration::query()
                    ->where('event_id', $event->getKey())
                    ->where('user_id', $user->getKey())
                    ->whereIn('status', collect(EventRegistrationStatus::memberResponses())->map->value->all())
                    ->whereHas('occurrence', fn ($query) => $query->where('starts', '>=', now()))
                    ->get()
                    ->each->delete();
            }

            return $registration;
        });
    }

    /**
     * The member's effective response for the occurrence.
     */
    public static function responseFor(OccurrenceData $occurrence, User $user): ?EventRegistrationStatus
    {
        return EventAttendanceService::statusFor($occurrence, $user);
    }

    /**
     * Effective registrations for the occurrence grouped by member response, each with its user loaded.
     *
     * @return Collection<string, Collection<int, EventRegistration>>
     */
    public static function responses(OccurrenceData $occurrence): Collection
    {
        $registrations = EventAttendanceService::registrationsFor($occurrence);
        $users = User::query()->whereKey($registrations->keys()->all())->get()->keyBy('id');

        return $registrations
            ->each(fn (EventRegistration $registration) => $registration->setRelation('user', $users->get($registration->user_id)))
            ->filter(fn (EventRegistration $registration): bool => $registration->getRelation('user') instanceof User)
            ->groupBy(fn (EventRegistration $registration): string => $registration->status?->value ?? EventRegistrationStatus::UNKNOWN->value);
    }

    /**
     * Response counts for the occurrence.
     *
     * @return array{going: int, maybe: int, not_going: int}
     */
    public static function summary(OccurrenceData $occurrence): array
    {
        $counts = EventAttendanceService::registrationsFor($occurrence)
            ->countBy(fn (EventRegistration $registration): string => $registration->status?->value ?? EventRegistrationStatus::UNKNOWN->value);

        return [
            'going' => (int) $counts->get(EventRegistrationStatus::GOING->value, 0) + (int) $counts->get(EventRegistrationStatus::ATTENDED->value, 0),
            'maybe' => (int) $counts->get(EventRegistrationStatus::TENTATIVE->value, 0) + (int) $counts->get(EventRegistrationStatus::INTERESTED->value, 0),
            'not_going' => (int) $counts->get(EventRegistrationStatus::NOT_GOING->value, 0) + (int) $counts->get(EventRegistrationStatus::DECLINED->value, 0),
        ];
    }

    protected static function closedReason(OccurrenceData $occurrence): ?EventRegistrationException
    {
        $event = $occurrence->event;

        return match (true) {
            $occurrence->isCancelled() => EventRegistrationException::cancelled(),
            $occurrence->hasEnded() => EventRegistrationException::ended(),
            ! $event->registration_enabled,
            $event->registration_deadline?->isPast() === true => EventRegistrationException::closed(),
            default => null,
        };
    }
}
