<?php

namespace App\Notifications\Records;

use App\Models\Records\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewAssignmentRecord extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var Assignment
     */
    protected $record;

    /**
     * @var array|string|string[]
     */
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Assignment $record)
    {
        $this->record = $record;
        $this->url = route('nova.pages.detail', [
            'resource' => \App\Nova\Records\Assignment::uriKey(),
            'resourceId' => $this->record->id,
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
        return (new MailMessage())->subject('New Assignment Record Added')
                                  ->line('A new assignment record has been added to your personnel file.')
                                  ->action('View Record', $this->url);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return array
     */
    public function toNova()
    {
        return (new NovaNotification())->message('A new assignment record has been added to your personnel file.')
                                       ->action('View Record', URL::remote($this->url))
                                       ->icon('document-text')
                                       ->type('info');
    }
}
