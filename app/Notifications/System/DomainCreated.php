<?php

namespace App\Notifications\System;

use App\Mail\System\DomainCreatedMail;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DomainCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Domain $domain)
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): DomainCreatedMail
    {
        return (new DomainCreatedMail($this->domain))->to($notifiable->email);
    }
}
