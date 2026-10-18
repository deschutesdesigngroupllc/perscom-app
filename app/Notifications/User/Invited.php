<?php

declare(strict_types=1);

namespace App\Notifications\User;

use App\Mail\User\Invited as InvitedMail;
use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class Invited extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invite $invite, public string $url, public string $organization)
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): InvitedMail
    {
        return new InvitedMail($this->invite, $this->url, $this->organization)->to($this->invite->email);
    }
}
