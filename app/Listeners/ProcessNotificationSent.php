<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Notifications\Tenant\NewMessage;
use Illuminate\Notifications\Events\NotificationSent;

class ProcessNotificationSent
{
    public function handle(NotificationSent $event): void
    {
        if ($event->notification instanceof NewMessage) {
            $message = $event->notification->message;

            $message->forceFill([
                'sent_at' => now(),
            ])->save();
        }
    }
}
