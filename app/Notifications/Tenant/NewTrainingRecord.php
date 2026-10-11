<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\TrainingRecordResource;
use App\Mail\Tenant\NewTrainingRecordMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Services\TwilioService;
use App\Support\Discord\DiscordEmbed;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewTrainingRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    protected string $message;

    public function __construct(protected TrainingRecord $trainingRecord)
    {
        $this->url = TrainingRecordResource::getUrl('view', [
            'record' => $this->trainingRecord,
        ], panel: 'app');

        $text = Str::limit($this->trainingRecord->text);

        $details = collect([
            'Event' => $this->trainingRecord->event?->name,
            'Instructor' => $this->trainingRecord->instructor?->name,
        ])->filter()->map(fn (string $value, string $label): string => sprintf('<strong>%s: </strong>%s<br>', $label, $value))->implode('');

        $this->message = <<<HTML
<p>A new training record has been added to your account.</p>
{$details}<strong>Text: </strong>{$text}
HTML;
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'New Training Record';
    }

    public static function notificationDescription(): string
    {
        return 'Sent anytime your account receives a new training record.';
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

    public function toMail(mixed $notifiable): NewTrainingRecordMail
    {
        return new NewTrainingRecordMail($this->trainingRecord, $this->url)->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Training Record')
            ->body($this->message)
            ->actions([
                Action::make('Open training record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make()
            ->title('New Training Record')
            ->body($this->message)
            ->actions([
                Action::make('Open training record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }

    public function toDiscord(): DiscordMessage
    {
        $embed = DiscordEmbed::make()
            ->title('New Training Record')
            ->description(Str::limit(strip_tags((string) $this->trainingRecord->text), 500))
            ->url($this->url)
            ->color('#0ea5e9')
            ->field('Member', $this->trainingRecord->user?->name)
            ->field('Event', $this->trainingRecord->event?->name)
            ->field('Instructor', $this->trainingRecord->instructor?->name)
            ->field('Author', $this->trainingRecord->author?->name)
            ->timestamp($this->trainingRecord->created_at);

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
}
