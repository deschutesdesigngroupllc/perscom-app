<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Mail\Tenant\PromotionEligibleMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\Rank;
use App\Models\User;
use App\Services\TwilioService;
use App\Support\Discord\DiscordEmbed;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class PromotionEligible extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $title = 'You are eligible for promotion';

    protected string $message;

    public function __construct(public Rank $rank)
    {
        $this->message = "<p>You now meet the requirements for promotion to <strong>{$this->rank->name}</strong>. Your leadership has been notified through the promotion board.</p>";
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'Promotion Eligible';
    }

    public static function notificationDescription(): string
    {
        return 'Sent when you first meet the requirements for your next rank.';
    }

    /**
     * @return string[]
     */
    public function via(User $notifiable): array
    {
        return collect(NotificationChannel::cases())
            ->reject(fn (NotificationChannel $channel): bool => $channel === NotificationChannel::DISCORD_PUBLIC)
            ->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled($notifiable))
            ->map(fn (NotificationChannel $channel): string => $channel->getChannel())
            ->values()
            ->all();
    }

    public function toMail(mixed $notifiable): PromotionEligibleMail
    {
        return new PromotionEligibleMail(
            title: $this->title,
            rank: $this->rank->name,
        )->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return $this->toFilamentNotification()->getBroadcastMessage();
    }

    public function toDatabase(): array
    {
        return $this->toFilamentNotification()->getDatabaseMessage();
    }

    public function toDiscord(): DiscordMessage
    {
        $embed = DiscordEmbed::make()
            ->title('Eligible For Promotion')
            ->description(sprintf('You now meet the requirements for promotion to %s.', $this->rank->name))
            ->color('#22c55e')
            ->thumbnail($this->rank->image?->image_url)
            ->field('Next Rank', $this->rank->name)
            ->timestamp(now());

        return DiscordMessage::create(body: '', embed: $embed->toArray());
    }

    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = resolve(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->message)
        )) {
            return null;
        }

        return $channel;
    }

    protected function toFilamentNotification(): FilamentNotification
    {
        return FilamentNotification::make()
            ->title($this->title)
            ->body($this->message)
            ->success();
    }
}
