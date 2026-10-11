<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Mail\Tenant\CredentialExpiringMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\User;
use App\Services\TwilioService;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class CredentialExpiring extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    /**
     * The number of days before expiration that a reminder is sent.
     *
     * @var int[]
     */
    public const array REMINDER_DAYS = [30, 14, 1];

    protected string $title;

    protected string $message;

    public function __construct(
        public string $name,
        public CarbonInterface $expiresAt,
        public int $daysUntilExpiry,
        public string $kind = 'credential',
        public ?string $url = null,
    ) {
        $date = $this->expiresAt->toFormattedDateString();

        $this->title = $this->daysUntilExpiry <= 0
            ? sprintf('Your %s has expired', $this->kind)
            : sprintf('Your %s is expiring soon', $this->kind);

        $this->message = $this->daysUntilExpiry <= 0
            ? "<p>Your {$this->kind} <strong>{$this->name}</strong> expired on {$date}. Please renew it as soon as possible.</p>"
            : "<p>Your {$this->kind} <strong>{$this->name}</strong> will expire in {$this->daysUntilExpiry} day(s) on {$date}.</p>";
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'Credential Expiring';
    }

    public static function notificationDescription(): string
    {
        return 'Sent 30, 14 and 1 day(s) before one of your credentials or qualifications expires, and on the day it expires.';
    }

    public static function shouldRemind(int $daysUntilExpiry): bool
    {
        return $daysUntilExpiry === 0 || in_array($daysUntilExpiry, self::REMINDER_DAYS, true);
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

    public function toMail(mixed $notifiable): CredentialExpiringMail
    {
        return new CredentialExpiringMail(
            title: $this->title,
            name: $this->name,
            kind: $this->kind,
            expiresAt: $this->expiresAt->toFormattedDateString(),
            daysUntilExpiry: $this->daysUntilExpiry,
            url: $this->url,
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
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);

        return DiscordMessage::create(
            body: $converter->convert($this->message)
        );
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
            ->when(filled($this->url), fn (FilamentNotification $notification): FilamentNotification => $notification->actions([
                Action::make('Open record')
                    ->button()
                    ->url($this->url),
            ]))
            ->when(
                $this->daysUntilExpiry <= 0,
                fn (FilamentNotification $notification): FilamentNotification => $notification->danger(),
                fn (FilamentNotification $notification): FilamentNotification => $notification->warning(),
            );
    }
}
