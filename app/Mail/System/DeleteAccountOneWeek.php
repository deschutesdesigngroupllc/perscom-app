<?php

namespace App\Mail\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccountOneWeek extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build(): static
    {
        return $this->markdown('emails.system.delete-account-one-week')
            ->subject('Account Deletion Warning');
    }
}
