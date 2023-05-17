<?php

namespace App\Notifications\User;

use App\Mail\User\PasswordChangedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification implements ShouldQueue
{
    use Queueable;

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
     * @return PasswordChangedMail
     */
    public function toMail($notifiable)
    {
        return (new PasswordChangedMail())->to($notifiable->email);
    }
}
