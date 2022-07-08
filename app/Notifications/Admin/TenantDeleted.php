<?php

namespace App\Notifications\Admin;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class TenantDeleted extends Notification
{
    use Queueable;

    /**
     * @var
     */
    protected $tenant;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', NovaChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Tenant Deleted')
            ->line('A tenant has been deleted.')
            ->action(
                'View Tenant',
                URL::remote(
                    route('nova.pages.detail', [
                        'resource' => 'tenants',
                        'resourceId' => $this->tenant->getTenantKey(),
                    ])
                )
            );
    }

    /**
     * Get the nova representation of the notification
     *
     * @return array
     */
    public function toNova()
    {
        return (new NovaNotification())
            ->message('A tenant has been deleted.')
            ->action(
                'View Tenant',
                URL::remote(
                    route('nova.pages.detail', [
                        'resource' => 'tenants',
                        'resourceId' => $this->tenant->getTenantKey(),
                    ])
                )
            )
            ->icon('user-remove')
            ->type('danger');
    }
}
