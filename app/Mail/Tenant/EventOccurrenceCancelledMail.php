<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\EventOccurrence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventOccurrenceCancelledMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected EventOccurrence $occurrence, protected string $date, protected string $url)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Cancelled: %s on %s', $this->occurrence->event?->name, $this->date),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.event-occurrence-cancelled',
            with: [
                'name' => $this->occurrence->event?->name,
                'date' => $this->date,
                'reason' => $this->occurrence->notes,
                'url' => $this->url,
            ]
        );
    }
}
