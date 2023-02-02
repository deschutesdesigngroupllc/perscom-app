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

    /**
     * @var array|string|string[]
     */
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected QualificationRecord $qualificationRecord)
    {
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\QualificationRecord::uriKey(),
            'resourceId' => $this->qualificationRecord->id,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', NovaChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new NewQualificationRecordMail($this->qualificationRecord, $this->url))->to($notifiable->email);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return array
     */
    public function toNova()
    {
        return (new NovaNotification())->message('A new qualification record has been added to your personnel file.')
                                       ->action('View Record', URL::remote($this->url))
                                       ->icon('document-text')
                                       ->type('info');
    }
}
