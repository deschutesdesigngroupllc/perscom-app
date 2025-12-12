<?php

declare(strict_types=1);

namespace App\Mail\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccountOneDay extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build(): static
    {
        return $this->markdown('emails.system.delete-account-one-day')
            ->subject('Your account will be deleted tomorrow - Final notice');
    }
}
