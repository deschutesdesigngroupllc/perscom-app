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

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected Mail $mail)
    {
        //
    }

    /**
     * @return array<string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): NewMailMailable
    {
        return (new NewMailMailable($this->mail))->to($notifiable->email);
    }
}
