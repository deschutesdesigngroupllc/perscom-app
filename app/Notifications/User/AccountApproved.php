<?php

namespace App\Notifications\User;

use App\Mail\User\AccountApproved as AccountApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var mixed|\Stancl\Tenancy\Contracts\Tenant|null
     */
    protected mixed $tenant;

    public function __construct()
    {
        $this->tenant = tenant();
    }

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
     * @return AccountApprovedMail
     */
    public function toMail(object $notifiable)
    {
        return (new AccountApprovedMail($this->tenant))->to($notifiable->email);
    }
}
