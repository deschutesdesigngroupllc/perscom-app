<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Mail\Tenant\NewServiceRecordMail;
use App\Models\ServiceRecord;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewServiceRecord extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected ServiceRecord $serviceRecord)
    {
        $this->url = ServiceRecordResource::getUrl('view', [
            'record' => $this->serviceRecord,
        ], panel: 'app');
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewServiceRecordMail
    {
        return (new NewServiceRecordMail($this->serviceRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $text = Str::limit($this->serviceRecord->text);

        return FilamentNotification::make()
            ->title('New Service Record')
            ->body(Str::markdown("A new service record has been added to your account.<br><br>**Text:** $text"))
            ->actions([
                Action::make('Open service record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        $text = Str::limit($this->serviceRecord->text);

        return FilamentNotification::make()
            ->title('New Service Record')
            ->body(Str::markdown("A new service record has been added to your account.<br><br>**Text:** $text"))
            ->actions([
                Action::make('Open service record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
