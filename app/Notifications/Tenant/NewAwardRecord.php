<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\AwardRecordResource;
use App\Mail\Tenant\NewAwardRecordMail;
use App\Models\AwardRecord;
use App\Models\Enums\NotificationGroup;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewAwardRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected AwardRecord $awardRecord)
    {
        $this->url = AwardRecordResource::getUrl('view', [
            'record' => $this->awardRecord,
        ], panel: 'app');
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

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewAwardRecordMail
    {
        return (new NewAwardRecordMail($this->awardRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Award Record')
            ->body(Str::markdown("A new award record has been added to your account.<br><br>**Award:** {$this->awardRecord?->award?->name}"))
            ->actions([
                Action::make('Open award record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Award Record')
            ->body(Str::markdown("A new award record has been added to your account.<br><br>**Award:** {$this->awardRecord?->award?->name}"))
            ->actions([
                Action::make('Open award record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
