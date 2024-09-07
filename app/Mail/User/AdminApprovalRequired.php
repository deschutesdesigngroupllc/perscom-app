<?php

declare(strict_types=1);

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminApprovalRequired extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected User $user, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.user.admin-approval-required')
            ->subject('User Approval Required')
            ->with([
                'user' => $this->user->name,
                'email' => $this->user->email,
                'url' => $this->url,
            ]);
    }
}
