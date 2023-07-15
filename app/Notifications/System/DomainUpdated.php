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
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): DomainUpdatedMail
    {
        return (new DomainUpdatedMail($this->domain))->to($notifiable->email);
    }
}
