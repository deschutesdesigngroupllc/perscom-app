<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\NewTenantMail;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewTenant extends Notification
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
        return (new NewTenantMail($this->tenant))->to($notifiable->email);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return array
     */
    public function toNova()
    {
        return (new NovaNotification())
            ->message('A new tenant has been created.')
            ->action(
                'View Tenant',
                URL::remote(
                    route('nova.pages.detail', [
                        'resource' => 'tenants',
                        'resourceId' => $this->tenant->getTenantKey(),
                    ])
                )
            )
            ->icon('user-add')
            ->type('success');
    }
}
