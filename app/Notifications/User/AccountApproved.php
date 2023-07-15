<?php

namespace App\Notifications\User;

use App\Mail\User\AccountApproved as AccountApprovedMail;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected ?Tenant $tenant = null;

    public function __construct()
    {
        $this->tenant = tenant();
    }

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): AccountApprovedMail
    {
        return (new AccountApprovedMail($this->tenant))->to($notifiable->email);
    }
}
