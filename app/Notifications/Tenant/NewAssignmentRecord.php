<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewAssignmentRecordMail;
use App\Models\AssignmentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewAssignmentRecord extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected AssignmentRecord $assignmentRecord)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\AssignmentRecord::uriKey(),
            'resourceId' => $this->assignmentRecord->id,
        ]);
    }

    /**
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewAssignmentRecordMail
    {
        return (new NewAssignmentRecordMail($this->assignmentRecord, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message('A new assignment record has been added to your personnel file.')
            ->action('View Record', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
