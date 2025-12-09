<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Filament\App\Resources\UserResource;
use App\Mail\Tenant\NewTaskAssignmentMail;
use App\Models\TaskAssignment;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewTaskAssignment extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    protected string $message;

    public function __construct(protected TaskAssignment $taskAssignment)
    {
        $this->url = UserResource::getUrl('view', [
            'record' => $this->taskAssignment->user,
        ], panel: 'app');

        $this->message = <<<HTML
<p>A new task has been assigned to you.</p>
<strong>Task: </strong>{$this->taskAssignment->task->title}
HTML;
    }

    /**
     * @return array<int, string>
     */
    public function via(): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewTaskAssignmentMail
    {
        return new NewTaskAssignmentMail($this->taskAssignment, $this->url)->to($notifiable->email);
    }

    public function toBroadcast(): BroadcastMessage
    {
        return FilamentNotification::make()
            ->title('New Task Assigned')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open task')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make()
            ->title('New Task Assigned')
            ->body(Str::markdown($this->message))
            ->actions([
                Action::make('Open task')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
