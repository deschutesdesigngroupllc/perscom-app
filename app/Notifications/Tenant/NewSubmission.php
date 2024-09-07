<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Filament\App\Resources\SubmissionResource;
use App\Mail\Tenant\NewSubmissionMail;
use App\Models\Submission;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    protected string $url;

    public function __construct(protected Submission $submission)
    {
        $this->url = SubmissionResource::getUrl('view', [
            'record' => $this->submission,
        ], panel: 'app');
    }

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): NewSubmissionMail
    {
        return (new NewSubmissionMail($this->submission, $this->url))->to($notifiable->email);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $name = optional($this->submission->form)->name;

        return FilamentNotification::make()
            ->title('New Form Submission')
            ->body("A new $name has been submitted.")
            ->actions([
                Action::make('Open submission')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getBroadcastMessage();
    }

    public function toDatabase($notifiable): array
    {
        $name = optional($this->submission->form)->name;

        return FilamentNotification::make()
            ->title('New Form Submission')
            ->body("A new $name has been submitted.")
            ->actions([
                Action::make('Open submission')
                    ->button()
                    ->url($this->url),
            ])
            ->info()
            ->getDatabaseMessage();
    }
}
