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

        $text = Str::limit($this->assignmentRecord->text);

        $this->message = <<<HTML
<p>A new assignment record has been added to your account.</p>
<strong>Type</strong>: {$this->assignmentRecord->type?->getLabel()}<br>
<strong>Position</strong>: {$this->assignmentRecord->position?->name}<br>
<strong>Specialty</strong>: {$this->assignmentRecord->specialty?->name}<br>
<strong>Unit</strong>: {$this->assignmentRecord->unit?->name}<br>
<strong>Status</strong>: {$this->assignmentRecord->status?->name}<br>
<strong>Text: </strong>$text
HTML;
    }

    public static function notificationGroup(): NotificationGroup
    {
        return NotificationGroup::RECORDS;
    }

    public static function notificationTitle(): string
    {
        return 'New Assignment Record';
    }

    public static function notificationDescription(): string
    {
        return 'Sent anytime your account receives a new assignment record.';
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

    public function toMail(User $notifiable): NewAssignmentRecordMail
    {
        return (new NewAssignmentRecordMail($this->assignmentRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Assignment Record')
            ->body($this->message)
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
            ->body($this->message)
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
