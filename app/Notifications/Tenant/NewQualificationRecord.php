<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewQualificationRecordMail;
use App\Models\QualificationRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewQualificationRecord extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $url;

    public function __construct(protected QualificationRecord $qualificationRecord)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\QualificationRecord::uriKey(),
            'resourceId' => $this->qualificationRecord->id,
        ]);
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewQualificationRecordMail
    {
        return (new NewQualificationRecordMail($this->qualificationRecord, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message('A new qualification record has been added to your personnel file.')
            ->action('View Record', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
