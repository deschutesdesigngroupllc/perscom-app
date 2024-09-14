<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Filament\App\Resources\QualificationRecordResource;
use App\Mail\Tenant\NewQualificationRecordMail;
use App\Models\QualificationRecord;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewQualificationRecord extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected QualificationRecord $qualificationRecord)
    {
        $this->url = QualificationRecordResource::getUrl('view', [
            'record' => $this->qualificationRecord,
        ], panel: 'app');
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewQualificationRecordMail
    {
        return (new NewQualificationRecordMail($this->qualificationRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Qualification Record')
            ->body(Str::markdown("A new qualification record has been added to your account.<br><br>**Qualification:** {$this->qualificationRecord?->qualification?->name}"))
            ->actions([
                Action::make('Open qualification record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Qualification Record')
            ->body(Str::markdown("A new qualification record has been added to your account.<br><br>**Qualification:** {$this->qualificationRecord?->qualification?->name}"))
            ->actions([
                Action::make('Open qualification record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
