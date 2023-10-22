<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewRankRecordMail;
use App\Models\RankRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewRankRecord extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $url;

    public function __construct(protected RankRecord $rankRecord)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\RankRecord::uriKey(),
            'resourceId' => $this->rankRecord->id,
        ]);
    }

    /**
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewRankRecordMail
    {
        return (new NewRankRecordMail($this->rankRecord, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message('A new rank record has been added to your personnel file.')
            ->action('View Record', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
