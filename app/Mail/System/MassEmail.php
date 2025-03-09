<?php

declare(strict_types=1);

namespace App\Mail\System;

use App\Models\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MassEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected Mail $mail)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mail->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.system.mass-email',
            with: [
                'content' => $this->mail->content,
            ]
        );
    }
}
