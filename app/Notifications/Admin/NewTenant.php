<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\NewTenantMail;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewTenant extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Tenant $tenant)
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewTenantMail
    {
        return (new NewTenantMail($this->tenant))->to($notifiable->email);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return NovaNotification
     */
    public function toNova()
    {
        return (new NovaNotification())->message('A new tenant has been created.')
            ->action('View Tenant', URL::remote(route('nova.pages.detail', [
                'resource' => 'tenants',
                'resourceId' => $this->tenant->getTenantKey(),
            ])))
            ->icon('user-add')
            ->type('success');
    }
}
