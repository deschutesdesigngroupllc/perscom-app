<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Facades\ContentTagParser;
use App\Models\Enums\NotificationChannel;
use App\Models\Event;
use App\Models\ModelNotification;
use App\Models\User;
use App\Pipes\ConvertHtmlToMarkdown;
use App\Services\TwilioService;
use App\Support\Discord\DiscordEmbed;
use App\Traits\HasResourceUrl;
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
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\HtmlString;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewModelNotification extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public $deleteWhenMissingModels = true;

    protected FilamentNotification $notification;

    protected string $subject;

    protected string $message;

    public function __construct(protected ModelNotification $modelNotification)
    {
        $this->subject = ContentTagParser::parse(
            content: $this->modelNotification->subject ?? 'Unable to parse notification subject.',
            attachedModel: $this->modelNotification->model
        ) ?? 'Unable to parse notification subject.';

        $this->message = ContentTagParser::parse(
            content: $this->modelNotification->message ?? 'Unable to parse notification message.',
            attachedModel: $this->modelNotification->model
        ) ?? 'Unable to parse notification message.';

        $this->notification = FilamentNotification::make()
            ->title($this->subject)
            ->body($this->message);

        $model = $this->modelNotification->model;
        if (filled($model) && $model instanceof HasColor) {
            $color = $model->getColor();

            match (true) {
                is_string($color) => $this->notification->color(Color::generateV3Palette($color)),
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
            ->reject(fn (NotificationChannel $channel): bool => $channel === NotificationChannel::DISCORD_PUBLIC)
            ->filter(fn (NotificationChannel $channel): bool => $channel->getEnabled($notifiable))
            ->map(fn (NotificationChannel $channel): string => $channel->getChannel())
            ->values()
            ->all();
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->subject)
            ->line(new HtmlString($this->message));
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
        $message = Pipeline::send($this->message)
            ->through([
                ConvertHtmlToMarkdown::class,
            ])->thenReturn();

        $model = $this->modelNotification->model;
        $color = $model instanceof HasColor ? $model->getColor() : null;

        $embed = DiscordEmbed::make()
            ->title($this->subject)
            ->description($message)
            ->url($this->getModelUrl())
            ->color(is_string($color) && str_starts_with($color, '#') ? $color : '#64748b')
            ->timestamp(now());

        return DiscordMessage::create(body: '', embed: $embed->toArray());
    }

    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = resolve(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->subject)
        )) {
            return null;
        }

        return $channel;
    }

    protected function getModelUrl(): ?string
    {
        $model = $this->modelNotification->model;

        if (blank($model) || ! in_array(HasResourceUrl::class, class_uses_recursive($model), true)) {
            return null;
        }

        return $model->getAttribute($model instanceof Event ? 'resource_url' : 'url');
    }
}
