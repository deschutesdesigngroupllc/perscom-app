<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Filament\Admin\Resources\TenantResource;
use App\Mail\Admin\TenantSetupFailedMail;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TenantSetupFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(protected Tenant $tenant, protected string $error)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(mixed $notifiable): TenantSetupFailedMail
    {
        return new TenantSetupFailedMail($this->tenant, $this->error)->to($notifiable->email);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(mixed $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Tenant Setup Failed')
            ->body(sprintf('%s could not be set up: %s', $this->tenant->name, $this->error))
            ->actions([
                Action::make('openTenant')
                    ->label('Open Tenant')
                    ->button()
                    ->url(TenantResource::getUrl('edit', ['record' => $this->tenant], panel: 'admin')),
            ])
            ->danger()
            ->getDatabaseMessage();
    }
}
