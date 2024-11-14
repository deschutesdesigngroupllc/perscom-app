<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\RankRecordResource;
use App\Mail\Tenant\NewRankRecordMail;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationGroup;
use App\Models\RankRecord;
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

        $text = Str::limit($this->rankRecord->text);

        $this->message = <<<HTML
<p>A new rank record has been added to your account.</p>
<strong>Rank: </strong>{$this->rankRecord->rank->name}<br>
<strong>Type: </strong>{$this->rankRecord->type->getLabel()}<br>
<strong>Text: </strong>$text
HTML;
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
        return 'Sent anytime your account receives a new rank record.';
    }

    /**
     * @return string[]
     */
    public function via(User $notifiable): array
    {
        return collect(NotificationChannel::cases())
            ->reject(fn (NotificationChannel $channel) => $channel === NotificationChannel::DISCORD_PUBLIC)
            ->filter(fn (NotificationChannel $channel) => $channel->getEnabled($notifiable))
            ->map(fn (NotificationChannel $channel) => $channel->getChannel())
            ->values()
            ->toArray();
    }

    public function toMail(mixed $notifiable): NewRankRecordMail
    {
        return (new NewRankRecordMail($this->rankRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Rank Record')
            ->body($this->message)
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
            ->body($this->message)
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
            body: $converter->convert($this->message)
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
