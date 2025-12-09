<?php

declare(strict_types=1);

namespace App\Notifications\User;

use App\Filament\App\Resources\UserResource;
use App\Mail\User\AdminApprovalRequired as AdminApprovalRequiredMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminApprovalRequired extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected User $user)
    {
        $this->url = UserResource::getUrl('edit', [
            'record' => $this->user,
        ], panel: 'app');
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): AdminApprovalRequiredMail
    {
        return new AdminApprovalRequiredMail($this->user, $this->url)->to($notifiable->email);
    }
}
