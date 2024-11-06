<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Notifications\Tenant\NewMessage;
use Illuminate\Notifications\Events\NotificationSent;

class ProcessNotificationSent
{
    public function handle(NotificationSent $event): void
    {
        if (is_a($event->notification, NewMessage::class)) {
            $message = $event->notification->message;

            $message->forceFill([
                'sent_at' => now(),
            ])->save();
        }
    }
}
