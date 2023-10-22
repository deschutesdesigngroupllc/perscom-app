<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewMail as NewMailMailable;
use App\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewMail extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Mail $mail)
    {
        //
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): NewMailMailable
    {
        return (new NewMailMailable($this->mail))->to($notifiable->email);
    }
}
