<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Filament\App\Resources\CombatRecordResource;
use App\Mail\Tenant\NewCombatRecordMail;
use App\Models\CombatRecord;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewCombatRecord extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected CombatRecord $combatRecord)
    {
        $this->url = CombatRecordResource::getUrl('view', [
            'record' => $this->combatRecord,
        ], panel: 'app');
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewCombatRecordMail
    {
        return (new NewCombatRecordMail($this->combatRecord, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Combat Record')
            ->body(Str::markdown("A new combat record has been added to your account.<br><br>**Text:** $text"))
            ->actions([
                Action::make('Open combat record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        $text = Str::limit($this->combatRecord->text);

        return FilamentNotification::make()
            ->title('New Combat Record')
            ->body(Str::markdown("A new combat record has been added to your account.<br><br>**Text:** $text"))
            ->actions([
                Action::make('Open combat record')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
