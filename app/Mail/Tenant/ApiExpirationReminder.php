<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class ApiExpirationReminder extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $name,
        public Carbon $expiresAt,
    ) {
        //
    }

    /**
     * @throws Exception
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have an API key expiring '.$this->expiresAt->diffForHumans(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.api-expiration-reminder',
            with: [
                'name' => $this->name,
                'expires_at' => $this->expiresAt,
            ]
        );
    }
}
