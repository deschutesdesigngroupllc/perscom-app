<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Mail\Tenant\UpcomingEvent as UpcomingEventMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\Enums\NotificationInterval;
use App\Models\Event;
use App\Models\User;
use App\Services\UserSettingsService;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class UpcomingEvent extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(public Event $event, protected NotificationInterval $interval) {}

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

    /**
     * @return string[]
     */
    public function via(): array
    {
        /** @var Collection<int, NotificationChannel> $channels */
        $channels = $this->event->notifications_channels;

        return $channels->map(fn (NotificationChannel $channel) => $channel->getChannel())->toArray();
    }

    public function toMail(User $notifiable): UpcomingEventMail
    {
        return (new UpcomingEventMail($this->event, $this->interval, $notifiable))->to($notifiable->email);
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title("Upcoming Event - {$this->event->name}")
            ->body($this->getMessage($notifiable))
            ->info()
            ->getBroadcastMessage();
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title("Upcoming Event - {$this->event->name}")
            ->body($this->getMessage($notifiable))
            ->info()
            ->getDatabaseMessage();
    }

    public function toDiscord(User $notifiable): DiscordMessage
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);

        return DiscordMessage::create($converter->convert($this->getMessage($notifiable)));
    }

    public function toTwilio(User $notifiable): TwilioSmsMessage|TwilioMessage
    {
        return (new TwilioSmsMessage)
            ->from(config('services.twilio.from'))
            ->content($this->getMessage($notifiable));
    }

    protected function getMessage(User $user): string
    {
        $timezone = UserSettingsService::get(
            key: 'timezone',
            default: config('app.timezone'),
            user: $user
        );

        $start = $this->event->starts->setTimezone($timezone)->shiftTimezone('UTC');

        $date = $start->toFormattedDayDateString();
        $time = $start->format('g:i A');

        return match ($this->interval) {
            NotificationInterval::PT0M => "{$this->event->name} is starting now.",
            NotificationInterval::PT15M => "{$this->event->name} is starting in 15 minutes.",
            NotificationInterval::PT1H => "{$this->event->name} is starting in 1 hour.",
            NotificationInterval::P1D => "{$this->event->name} begins tomorrow at $time.",
            NotificationInterval::P1W => "{$this->event->name} begins next week on $date at $time.",
        };
    }
}
