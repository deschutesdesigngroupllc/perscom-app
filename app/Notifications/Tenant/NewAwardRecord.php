<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewAwardRecordMail;
use App\Models\AwardRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewAwardRecord extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $url;

    public function __construct(protected AwardRecord $awardRecord)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\AwardRecord::uriKey(),
            'resourceId' => $this->awardRecord->id,
        ]);
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewAwardRecordMail
    {
        return (new NewAwardRecordMail($this->awardRecord, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message('A new award record has been added to your personnel file.')
            ->action('View Record', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
