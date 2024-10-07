<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Message $message)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Message',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.new-message',
            with: [
                'message' => $this->message->message,
            ]
        );
    }
}
