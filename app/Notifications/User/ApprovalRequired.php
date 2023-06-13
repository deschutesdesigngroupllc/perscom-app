<?php

namespace App\Notifications\User;

use App\Mail\User\ApprovalRequired as ApprovalRequiredMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ApprovalRequired extends Notification implements ShouldQueue
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
     * @return ApprovalRequiredMail
     */
    public function toMail(object $notifiable)
    {
        return (new ApprovalRequiredMail())->to($notifiable->email);
    }
}
