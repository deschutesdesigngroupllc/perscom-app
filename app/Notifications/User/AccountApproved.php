<?php

declare(strict_types=1);

namespace App\Notifications\User;

use App\Mail\User\AccountApproved as AccountApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected mixed $tenant;

    public function __construct()
    {
        $this->tenant = tenant();
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): AccountApprovedMail
    {
        return new AccountApprovedMail($this->tenant)->to($notifiable->email);
    }
}
