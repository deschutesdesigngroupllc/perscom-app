<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Mail\Tenant\RegistrationVerificationMail;
use App\Models\Registration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RegistrationVerificationRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Registration $registration)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): RegistrationVerificationMail
    {
        return (new RegistrationVerificationMail($this->registration))->to($notifiable->email);
    }
}
