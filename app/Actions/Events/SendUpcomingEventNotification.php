<?php

declare(strict_types=1);

namespace App\Actions\Events;

use App\Data\Events\OccurrenceData;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Notifications\Tenant\UpcomingEvent;
use App\Services\EventAttendanceService;
use App\Services\EventCalendarService;
use App\Services\EventOccurrenceService;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendUpcomingEventNotification
{
    /**
     * @throws Throwable
     */
    public static function handle(Event $event, NotificationInterval $interval, ?CarbonInterface $sendAt = null): void
    {
        if (! SendUpcomingEventNotification::canSendNotification($event)) {
            return;
        }

        Notification::send(SendUpcomingEventNotification::recipients($event), new UpcomingEvent($event, $interval, $sendAt));

        NotificationChannel::organizationWide($event->notifications_channels)->each(fn (NotificationChannel $channel) => Notification::sendNow(User::first(), new UpcomingEvent($event, $interval, $sendAt), [$channel->getChannel()]));
    }

    public static function canSendNotification(Event $event): bool
    {
        $event->loadMissing(['schedule']);

        if (filled($event->schedule) && $event->schedule->has_passed) {
            return false;
        }

        if (filled($event->schedule) && blank($event->schedule->next_occurrence)) {
            return false;
        }

        return filled($event->notifications_channels)
            && filled($event->notifications_interval)
            && $event->registration_enabled
            && $event->notifications_enabled
            && SendUpcomingEventNotification::recipients($event)->isNotEmpty();
    }

    /**
     * The members whose effective response for the upcoming occurrence is Going or Maybe. A response
     * made for the occurrence itself takes precedence over the member's series-wide response.
     *
     * @return Collection<int, User>
     */
    protected static function recipients(Event $event): Collection
    {
        $occurrence = EventOccurrenceService::nearest($event, $event->schedule?->next_occurrence ?? $event->starts);

        if (! $occurrence instanceof OccurrenceData || $occurrence->isCancelled()) {
            return new Collection;
        }

        $userIds = EventAttendanceService::registrationsFor($occurrence)
            ->filter(fn (EventRegistration $registration): bool => in_array($registration->status, EventCalendarService::NOTIFIED_RESPONSES, true))
            ->keys()
            ->all();

        return User::query()->whereKey($userIds)->get();
    }
}
