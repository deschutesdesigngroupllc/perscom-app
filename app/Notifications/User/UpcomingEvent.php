<?php

declare(strict_types=1);

namespace App\Notifications\User;

use App\Contracts\NotificationCanBeManaged;
use App\Models\Enums\NotificationGroup;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingEvent extends Notification implements NotificationCanBeManaged, ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(protected Event $event, protected NotificationInterval $interval)
    {
        //
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::EVENTS;
    }

    public static function notificationTitle(): string
    {
        return 'Upcoming Events';
    }

    public static function notificationDescription(): string
    {
        return 'Reminder notifications when an event is upcoming';
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
