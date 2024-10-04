<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Contracts\NotificationCanBeManaged;
use App\Filament\App\Resources\AssignmentRecordResource;
use App\Mail\Tenant\NewAssignmentRecordMail;
use App\Models\AssignmentRecord;
use App\Models\Enums\NotificationGroup;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewAssignmentRecord extends Notification implements NotificationCanBeManaged, ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected AssignmentRecord $assignmentRecord)
    {
        $this->url = AssignmentRecordResource::getUrl('view', [
            'record' => $this->assignmentRecord,
        ], panel: 'app');
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

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewAssignmentRecordMail
    {
        return (new NewAssignmentRecordMail($this->assignmentRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Assignment Record')
            ->body(Str::markdown("A new assignment record has been added to your account.<br><br>**Type:** {$this->assignmentRecord?->type?->getLabel()}<br>**Position:** {$this->assignmentRecord?->position?->name}<br>**Specialty:** {$this->assignmentRecord?->specialty?->name}<br>**Unit:** {$this->assignmentRecord?->unit?->name}<br>**Status:** {$this->assignmentRecord?->status?->name}"))
            ->actions([
                Action::make('Open assignment record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Assignment Record')
            ->body(Str::markdown("A new assignment record has been added to your account.<br><br>**Type:** {$this->assignmentRecord?->type?->getLabel()}<br>**Position:** {$this->assignmentRecord?->position?->name}<br>**Specialty:** {$this->assignmentRecord?->specialty?->name}<br>**Unit:** {$this->assignmentRecord?->unit?->name}<br>**Status:** {$this->assignmentRecord?->status?->name}"))
            ->actions([
                Action::make('Open assignment record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
