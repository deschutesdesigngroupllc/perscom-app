<?php

namespace App\Notifications\User;

use App\Mail\User\AdminApprovalRequired as AdminApprovalRequiredMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminApprovalRequired extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $url;

    /**
     * Constructor
     */
    public function __construct(protected User $user)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\User::uriKey(),
            'resourceId' => $this->user->getKey(),
        ]);
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
     * @return AdminApprovalRequiredMail
     */
    public function toMail(object $notifiable)
    {
        return (new AdminApprovalRequiredMail($this->user, $this->url))->to($notifiable->email);
    }
}
