<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewAnnouncement as NewAnnouncementMail;
use App\Models\Announcement;
use App\Models\Enums\NotificationChannel;
use App\Models\User;
use App\Services\TwilioService;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Colors\Color;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    protected FilamentNotification $notification;

    public function __construct(public Announcement $announcement)
    {
        $this->notification = FilamentNotification::make()
            ->title($this->announcement->title)
            ->body($this->announcement->content)
            ->color(Color::hex($this->announcement->color));
    }

    /**
     * @return string[]
     */
    public function via(): array
    {
        /** @var Collection<int, NotificationChannel> $channels */
        $channels = $this->announcement->channels;

        return $channels
            ->reject(fn (NotificationChannel $channel) => $channel === NotificationChannel::DISCORD_PUBLIC)
            ->map(fn (NotificationChannel $channel) => $channel->getChannel())
            ->values()
            ->toArray();
    }

    public function toMail(User $notifiable): NewAnnouncementMail
    {
        return (new NewAnnouncementMail($this->announcement))->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return $this->notification->getBroadcastMessage();
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(): array
    {
        return $this->notification->getDatabaseMessage();
    }

    public function toDiscord(): DiscordMessage
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);

        return DiscordMessage::create($converter->convert($this->announcement->content));
    }

    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = app(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->announcement->content)
        )) {
            return null;
        }

        return $channel;
    }
}
