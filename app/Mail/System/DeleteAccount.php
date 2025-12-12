<?php

declare(strict_types=1);

namespace App\Mail\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccount extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function build(): static
    {
        return $this->markdown('emails.system.delete-account')
            ->subject('Your account has been deleted');
    }
}
