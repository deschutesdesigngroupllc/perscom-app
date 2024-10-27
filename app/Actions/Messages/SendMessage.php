<?php

declare(strict_types=1);

namespace App\Actions\Messages;

use App\Models\Enums\NotificationChannel;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Channels\DiscordPublicChannel;
use App\Notifications\Tenant\NewMessage;
use Illuminate\Support\Facades\Notification;
use Throwable;

class SendMessage
{
    /**
     * @throws Throwable
     */
    public static function handle(Message $message): void
    {
        if (blank($message->channels)) {
            return;
        }

        $recipients = filled($message->recipients)
            ? $message->recipients->map(fn ($id) => User::find($id))
            : User::all();

        Notification::send($recipients, new NewMessage($message));

        if (collect($message->channels)->contains(NotificationChannel::DISCORD_PUBLIC)) {
            Notification::sendNow(User::first(), new NewMessage($message), [DiscordPublicChannel::class]);
        }
    }
}
