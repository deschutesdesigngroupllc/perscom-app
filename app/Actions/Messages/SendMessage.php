<?php

declare(strict_types=1);

namespace App\Actions\Messages;

use App\Models\Message;
use App\Models\User;
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
        $recipients = filled($message->recipients) || $message->recipients
            ? $message->recipients
            : User::all();

        Notification::send($recipients, new NewMessage($message));
    }
}
