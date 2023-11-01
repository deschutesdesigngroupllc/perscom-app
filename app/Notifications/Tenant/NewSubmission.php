<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewSubmissionMail;
use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $url;

    public function __construct(protected Submission $submission)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\Submission::uriKey(),
            'resourceId' => $this->submission->id,
        ]);
    }

    /**
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewSubmissionMail
    {
        return (new NewSubmissionMail($this->submission, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message("A new {$this->submission->form?->name} has been submitted.")
            ->action("View {$this->submission->form?->name}", URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
