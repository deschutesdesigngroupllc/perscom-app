<?php

namespace App\Mail\Tenant;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubmissionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission, public string $url)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New {$this->submission->form?->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.new-submission',
        );
    }
}
