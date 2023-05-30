<?php

namespace App\Notifications\System;

use App\Mail\System\DeleteAccount as DeleteAccountMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeleteAccount extends Notification implements ShouldQueue
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
     * @return MailMessage
     */
    public function toMail(object $notifiable)
    {
        return (new DeleteAccountMail())->to($notifiable->email);
    }
}
