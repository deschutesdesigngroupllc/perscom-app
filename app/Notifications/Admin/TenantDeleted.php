<?php

namespace App\Notifications\Admin;

use App\Mail\Admin\TenantDeletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;

class TenantDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $tenant, protected string $email)
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): TenantDeletedMail
    {
        return (new TenantDeletedMail($this->tenant, $this->email))->to($notifiable->email);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return NovaNotification
     */
    public function toNova()
    {
        return (new NovaNotification())->message('A tenant has been deleted.')
            ->icon('user-remove')
            ->type('danger');
    }
}
