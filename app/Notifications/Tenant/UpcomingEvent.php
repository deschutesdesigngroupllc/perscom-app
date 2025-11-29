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
use App\Services\TwilioService;
use App\Services\UserSettingsService;
use Carbon\CarbonInterface;
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

    public function __construct(public Event $event, protected NotificationInterval $interval, protected ?CarbonInterface $sendAt = null)
    {
        if (filled($this->sendAt)) {
            $this->delay(now()->diff($sendAt));
        }
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

    /**
     * @return string[]
     */
    public function via(): array
    {
        /** @var Collection<int, NotificationChannel> $channels */
        $channels = $this->event->notifications_channels;

        return $channels
            ->reject(fn (NotificationChannel $channel): bool => $channel === NotificationChannel::DISCORD_PUBLIC)
            ->map(fn (NotificationChannel $channel): string => $channel->getChannel())
            ->values()
            ->toArray();
    }

    public function toMail(User $notifiable): UpcomingEventMail
    {
        return (new UpcomingEventMail($this->event, $this->interval, $notifiable))->to($notifiable->email);
    }

    public function toBroadcast(User $notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('Upcoming Event - '.$this->event->name)
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
            ->title('Upcoming Event - '.$this->event->name)
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

    public function toTwilio(User $notifiable): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = app(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->getMessage($notifiable))
        )) {
            return null;
        }

        return $channel;
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
            NotificationInterval::PT0M => $this->event->name.' is starting now.',
            NotificationInterval::PT15M => $this->event->name.' is starting in 15 minutes.',
            NotificationInterval::PT1H => $this->event->name.' is starting in 1 hour.',
            NotificationInterval::P1D => sprintf('%s begins tomorrow at %s.', $this->event->name, $time),
            NotificationInterval::P1W => sprintf('%s begins next week on %s at %s.', $this->event->name, $date, $time),
        };
    }
}
