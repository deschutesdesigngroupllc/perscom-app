<?php

namespace App\Mail\System;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeleteAccountOneMonth extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.system.delete-account-one-month')->subject('Account Deletion Warning');
    }
}
