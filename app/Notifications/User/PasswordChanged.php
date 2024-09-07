<?php

declare(strict_types=1);

namespace App\Notifications\User;

use App\Mail\User\PasswordChangedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): PasswordChangedMail
    {
        return (new PasswordChangedMail)->to($notifiable->email);
    }
}
