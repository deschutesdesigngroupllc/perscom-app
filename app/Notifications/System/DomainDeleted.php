<?php

declare(strict_types=1);

namespace App\Notifications\System;

use App\Mail\System\DomainDeletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DomainDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $domain, protected string $url)
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): DomainDeletedMail
    {
        return (new DomainDeletedMail($this->domain, $this->url))->to($notifiable->email);
    }
}
