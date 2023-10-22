<?php

namespace App\Mail\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovalRequired extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build(): static
    {
        return $this->markdown('emails.user.approval-required')
            ->subject('Admin Approval Required');
    }
}
