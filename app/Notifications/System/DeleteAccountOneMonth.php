<?php

declare(strict_types=1);

namespace App\Notifications\System;

use App\Mail\System\DeleteAccountOneMonth as DeleteAccountMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DeleteAccountOneMonth extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): DeleteAccountMail
    {
        return (new DeleteAccountMail)->to($notifiable->email);
    }
}
