<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\AwardRecordResource;
use App\Mail\Tenant\NewAwardRecordMail;
use App\Models\AwardRecord;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\User;
use App\Services\TwilioService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use League\HTMLToMarkdown\HtmlConverter;
use NotificationChannels\Discord\DiscordMessage;
use NotificationChannels\Twilio\TwilioMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewAwardRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    protected string $message;

    public function __construct(protected AwardRecord $awardRecord)
    {
        $this->url = AwardRecordResource::getUrl('view', [
            'record' => $this->awardRecord,
        ], panel: 'app');

        $this->message = "A new award record has been added to your account.<br><br>**Award:** {$this->awardRecord?->award?->name}";
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'New Award Record';
    }

    public static function notificationDescription(): string
    {
        return 'Sent when anytime your account receives a new award record.';
    }

    /**
     * @return string[]
     */
    public function via(User $notifiable): array
    {
        return collect(NotificationChannel::cases())
            ->filter(fn (NotificationChannel $channel) => $channel->getEnabled($notifiable))
            ->map(fn (NotificationChannel $channel) => $channel->getChannel())
            ->values()
            ->toArray();
    }

    public function toMail(mixed $notifiable): NewAwardRecordMail
    {
        return (new NewAwardRecordMail($this->awardRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Award Record')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open award record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make()
            ->title('New Award Record')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open award record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }

    public function toDiscord(): DiscordMessage
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);

        return DiscordMessage::create(
            body: $converter->convert(Str::markdown($this->message))
        );
    }

    public function toTwilio(): TwilioSmsMessage|TwilioMessage|null
    {
        /** @var TwilioService $service */
        $service = app(TwilioService::class);

        if (! $channel = $service->toNotificationChannel(
            message: TwilioService::formatText($this->message)
        )) {
            return null;
        }

        return $channel;
    }
}
