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

    protected string $url;

    public function __construct(protected TaskAssignment $taskAssignment)
    {
        $this->url = route('nova.pages.lens', [
            'resource' => \App\Nova\TaskAssignment::uriKey(),
            'lens' => (new MyTasks())->uriKey(),
        ]);
    }

    /**
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail', NovaChannel::class];
    }

    public function toMail(mixed $notifiable): NewTaskAssignmentMail
    {
        return (new NewTaskAssignmentMail($this->taskAssignment, $this->url))->to($notifiable->email);
    }

    public function toNova(): NovaNotification
    {
        return (new NovaNotification())->message('A new task has been assigned to you.')
            ->action('View Tasks', URL::remote($this->url))
            ->icon('document-text')
            ->type('info');
    }
}
