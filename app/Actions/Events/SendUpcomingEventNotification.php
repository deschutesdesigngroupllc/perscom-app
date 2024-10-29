<?php

declare(strict_types=1);

namespace App\Actions\Events;

use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\User;
use App\Notifications\Channels\DiscordPublicChannel;
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
        if (blank($event->registrations)) {
            return;
        }

        if (blank($event->notifications_channels)) {
            return;
        }

        Notification::send($event->registrations, new UpcomingEvent($event, $interval, $sendAt));

        if (collect($event->notifications_channels)->contains(NotificationChannel::DISCORD_PUBLIC)) {
            Notification::sendNow(User::first(), new UpcomingEvent($event, $interval, $sendAt), [DiscordPublicChannel::class]);
        }
    }
}
