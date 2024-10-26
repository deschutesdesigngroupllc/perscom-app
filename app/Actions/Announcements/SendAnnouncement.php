<?php

declare(strict_types=1);

namespace App\Actions\Announcements;

use App\Models\Announcement;
use App\Models\Enums\NotificationChannel;
use App\Models\User;
use App\Notifications\Channels\DiscordPublicChannel;
use App\Notifications\Tenant\NewAnnouncement;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendAnnouncement
{
    /**
     * @throws Throwable
     */
    public static function handle(Announcement $announcement): void
    {
        if (blank($announcement->channels)) {
            return;
        }

        Notification::send(User::all(), new NewAnnouncement($announcement));

        if (collect($announcement->channels)->contains(NotificationChannel::DISCORD_PUBLIC)) {
            Notification::sendNow(User::first(), new NewAnnouncement($announcement), [DiscordPublicChannel::class]);
        }
    }
}
