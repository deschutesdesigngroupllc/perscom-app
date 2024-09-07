<?php

declare(strict_types=1);

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
        $name = optional($this->submission->form)->name;

        return new Envelope(
            subject: "New $name",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tenant.new-submission',
        );
    }
}
