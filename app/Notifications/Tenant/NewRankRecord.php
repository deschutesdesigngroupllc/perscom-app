<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\RankRecordResource;
use App\Mail\Tenant\NewRankRecordMail;
use App\Models\Enums\NotificationGroup;
use App\Models\RankRecord;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewRankRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected RankRecord $rankRecord)
    {
        $this->url = RankRecordResource::getUrl('view', [
            'record' => $this->rankRecord,
        ], panel: 'app');
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

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewRankRecordMail
    {
        return (new NewRankRecordMail($this->rankRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Rank Record')
            ->body(Str::markdown("A new rank record has been added to your account.<br><br>**Type:** {$this->rankRecord?->type?->getLabel()}<br>**Rank:** {$this->rankRecord?->rank?->name}"))
            ->actions([
                Action::make('Open rank record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Rank Record')
            ->body(Str::markdown("A new rank record has been added to your account.<br><br>**Type:** {$this->rankRecord?->type?->getLabel()}<br>**Rank:** {$this->rankRecord?->rank?->name}"))
            ->actions([
                Action::make('Open rank record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
