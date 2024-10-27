<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAnnouncement extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Announcement $announcement)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->announcement->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.new-announcement',
            with: [
                'title' => $this->announcement->title,
                'content' => $this->announcement->content,
            ]
        );
    }
}
