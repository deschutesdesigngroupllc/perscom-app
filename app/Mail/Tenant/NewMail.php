<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Mail $mail)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.mail')
            ->subject($this->mail->subject)
            ->with([
                'content' => $this->mail->content,
                'links' => $this->mail->links ?? [],
            ]);
    }
}
