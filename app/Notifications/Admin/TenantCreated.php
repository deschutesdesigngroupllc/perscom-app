<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Filament\Admin\Resources\TenantResource;
use App\Mail\Admin\TenantCreatedMail;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TenantCreated extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): TenantCreatedMail
    {
        return (new TenantCreatedMail($this->tenant))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Tenant Created')
            ->body('A new tenant has been created.')
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
            ->title('New Tenant Created')
            ->body('A new tenant has been created.')
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
