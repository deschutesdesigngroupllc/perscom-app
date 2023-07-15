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
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): ApprovalRequiredMail
    {
        return (new ApprovalRequiredMail())->to($notifiable->email);
    }
}
