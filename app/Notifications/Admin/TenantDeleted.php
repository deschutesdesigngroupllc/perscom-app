<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Filament\Admin\Resources\TenantResource;
use App\Mail\Admin\TenantDeletedMail;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TenantDeleted extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(protected string $tenant, protected string $email)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): TenantDeletedMail
    {
        return (new TenantDeletedMail($this->tenant, $this->email))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('Tenant Deleted')
            ->body('A tenant has been deleted.')
            ->actions([
                Action::make('View tenant')
                    ->button()
                    ->url(TenantResource::getUrl('edit', [
                        'record' => $this->tenant,
                    ], panel: 'admin')),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('Tenant Deleted')
            ->body('A tenant has been deleted.')
            ->actions([
                Action::make('View tenant')
                    ->button()
                    ->url(TenantResource::getUrl('edit', [
                        'record' => $this->tenant,
                    ], panel: 'admin')),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
