<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Models\Enums\NotificationChannel;
use App\Models\ModelNotification;
use App\Models\User;
use App\Services\TwilioService;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewModelNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected FilamentNotification $notification;

    public function __construct(protected ModelNotification $modelNotification)
    {
        $this->notification = FilamentNotification::make()
            ->title($this->modelNotification->subject)
            ->body($this->modelNotification->message);

        $model = $this->modelNotification->model;
        if (filled($model) && $model instanceof HasColor) {
            $color = $model->getColor();

            match (true) {
                is_string($color) => $this->notification->color(Color::hex($color)),
                is_array($color) => $this->notification->color($color),
                default => $this->notification->info()
            };
        }
    }

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        /** @var Collection<int, NotificationChannel> $channels */
        $channels = $this->modelNotification->channels;

        return $channels
            ->reject(fn (NotificationChannel $channel) => $channel === NotificationChannel::DISCORD_PUBLIC)
            ->filter(fn (NotificationChannel $channel) => $channel->getEnabled($notifiable))
            ->map(fn (NotificationChannel $channel) => $channel->getChannel())
            ->values()
            ->toArray();
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject($this->modelNotification->subject ?? 'You have received a new notification')
            ->greeting($this->modelNotification->subject ?? 'You have received a new notification.')
            ->line(new HtmlString($this->modelNotification->message ?? 'Unable to parse message.'));
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

        $html = <<<HTML
<p>{$this->modelNotification->subject}</p>
{$this->modelNotification->message}
HTML;

        return DiscordMessage::create($converter->convert($html));
    }

    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = app(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->modelNotification->subject ?? 'Unable to parse message.')
        )) {
            return null;
        }

        return $channel;
    }
}
