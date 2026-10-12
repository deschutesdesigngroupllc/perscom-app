<?php

declare(strict_types=1);

namespace App\Actions\Events;

use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\User;
use App\Notifications\Tenant\UpcomingEvent;
use Carbon\CarbonInterface;
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

        Notification::send($event->registrations, new UpcomingEvent($event, $interval, $sendAt));

        NotificationChannel::organizationWide($event->notifications_channels)->each(fn (NotificationChannel $channel) => Notification::sendNow(User::first(), new UpcomingEvent($event, $interval, $sendAt), [$channel->getChannel()]));
    }

    public static function canSendNotification(Event $event): bool
    {
        $event->loadMissing(['schedule', 'registrations']);

        if (filled($event->schedule) && $event->schedule->has_passed) {
            return false;
        }

        if (filled($event->schedule) && blank($event->schedule->next_occurrence)) {
            return false;
        }

        return filled($event->registrations)
            && filled($event->notifications_channels)
            && filled($event->notifications_interval)
            && $event->registration_enabled
            && $event->notifications_enabled;
    }
}
