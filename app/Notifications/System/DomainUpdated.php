<?php

namespace App\Notifications\System;

use App\Mail\System\DomainUpdatedMail;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DomainUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Domain $domain)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * @return DomainUpdatedMail
     */
    public function toMail($notifiable)
    {
        return (new DomainUpdatedMail($this->domain))->to($notifiable->email);
    }
}
