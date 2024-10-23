<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\RankRecordResource;
use App\Mail\Tenant\NewRankRecordMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\RankRecord;
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

class NewRankRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    protected string $message;

    public function __construct(protected RankRecord $rankRecord)
    {
        $this->url = RankRecordResource::getUrl('view', [
            'record' => $this->rankRecord,
        ], panel: 'app');

        $this->message = "A new rank record has been added to your account.<br><br>**Type:** {$this->rankRecord?->type?->getLabel()}<br>**Rank:** {$this->rankRecord?->rank?->name}";
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'New Rank Record';
    }

    public static function notificationDescription(): string
    {
        return 'Sent when anytime your account receives a new rank record.';
    }

    /**
     * @return string[]
     */
    public function via(): array
    {
        return collect(NotificationChannel::cases())->filter(function (NotificationChannel $channel) {
            return $channel->getEnabled();
        })->map(function (NotificationChannel $channel) {
            return $channel->getChannel();
        })->toArray();
    }

    public function toMail(mixed $notifiable): NewRankRecordMail
    {
        return (new NewRankRecordMail($this->rankRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Rank Record')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open rank record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make()
            ->title('New Rank Record')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open rank record')
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
