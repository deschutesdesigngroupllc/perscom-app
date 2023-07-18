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

    /**
     * @var array|string|string[]
     */
    protected $url;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Submission $submission)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\Submission::uriKey(),
            'resourceId' => $this->submission->id,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): NewSubmissionMail
    {
        return (new NewSubmissionMail($this->submission, $this->url))->to($notifiable->email);
    }

    /**
     * @return NovaNotification
     */
    public function toNova()
    {
        return (new NovaNotification())->message("A new {$this->submission->form?->name} has been submitted.")
            ->action("View {$this->submission->form?->name}", URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
