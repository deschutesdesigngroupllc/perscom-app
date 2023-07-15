<?php

namespace App\Notifications\System;

use App\Mail\System\DeleteAccountOneMonth as DeleteAccountMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DeleteAccountOneMonth extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): DeleteAccountMail
    {
        return (new DeleteAccountMail())->to($notifiable->email);
    }
}
