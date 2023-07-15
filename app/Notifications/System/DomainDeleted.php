<?php

namespace App\Notifications\System;

use App\Mail\System\DomainDeletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DomainDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected string $domain, protected string $url)
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

    public function toMail(mixed $notifiable): DomainDeletedMail
    {
        return (new DomainDeletedMail($this->domain, $this->url))->to($notifiable->email);
    }
}
