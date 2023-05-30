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
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * @return DeleteAccountMail
     */
    public function toMail(object $notifiable)
    {
        return (new DeleteAccountMail())->to($notifiable->email);
    }
}
