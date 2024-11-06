<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\AssignmentRecordResource;
use App\Mail\Tenant\NewAssignmentRecordMail;
use App\Models\AssignmentRecord;
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

class NewAssignmentRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    protected string $message;

    public function __construct(protected AssignmentRecord $assignmentRecord)
    {
        $this->url = AssignmentRecordResource::getUrl('view', [
            'record' => $this->assignmentRecord,
        ], panel: 'app');

        $this->message = "A new assignment record has been added to your account.<br><br>**Type:** {$this->assignmentRecord?->type?->getLabel()}<br>**Position:** {$this->assignmentRecord?->position?->name}<br>**Specialty:** {$this->assignmentRecord?->specialty?->name}<br>**Unit:** {$this->assignmentRecord?->unit?->name}<br>**Status:** {$this->assignmentRecord?->status?->name}";
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'New Combat Record';
    }

    public static function notificationDescription(): string
    {
        return 'Sent when anytime your account receives a new combat record.';
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

    public function toMail(User $notifiable): NewAssignmentRecordMail
    {
        return (new NewAssignmentRecordMail($this->assignmentRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Assignment Record')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open assignment record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make()
            ->title('New Assignment Record')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open assignment record')
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
