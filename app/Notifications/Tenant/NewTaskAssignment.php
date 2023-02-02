<?php

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewTaskAssignmentMail;
use App\Models\TaskAssignment;
use App\Nova\Lenses\MyTasks;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\URL;

class NewTaskAssignment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var string
     */
    protected $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected TaskAssignment $taskAssignment)
    {
        $this->url = route('nova.pages.lens', [
            'resource' => \App\Nova\TaskAssignment::uriKey(),
            'lens' => (new MyTasks())->uriKey(),
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
        return (new NewTaskAssignmentMail($this->taskAssignment, $this->url))->to($notifiable->email);
    }

    /**
     * Get the nova representation of the notification
     *
     * @return array
     */
    public function toNova()
    {
        return (new NovaNotification())->message('A new task has been assigned to you.')
                                       ->action('View Tasks', URL::remote($this->url))
                                       ->icon('document-text')
                                       ->type('info');
    }
}
