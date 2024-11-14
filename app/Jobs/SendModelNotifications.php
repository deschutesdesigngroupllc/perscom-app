<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Contracts\SendsModelNotifications;
use App\Models\Enums\NotificationChannel;
use App\Models\ModelNotification;
use App\Models\User;
use App\Notifications\Channels\DiscordPublicChannel;
use App\Notifications\Tenant\NewModelNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendModelNotifications implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected SendsModelNotifications|Model $model, protected string $event)
    {
        $this->afterCommit = true;
    }

    public function handle(): void
    {
        $this->model->loadMissing('modelNotifications');

        if (blank($this->model->modelNotifications)) {
            return;
        }

        $recipients = collect();

        $modelNotifications = $this->model->modelNotifications()->where('event', $this->event)->get();
        $modelNotifications->each(function (ModelNotification $modelNotification) use ($recipients) {
            $notificationRecipients = $modelNotification->getRecipients();
            if (filled($notificationRecipients)) {
                $notificationRecipients->each(function (User $user) use ($modelNotification, $recipients) {
                    $recipients->put($user->getKey(), $modelNotification);
                });
            }
        });

        $recipients->each(function (ModelNotification $modelNotification, $userId) {
            $user = User::findOrFail($userId);
            $user->notify(new NewModelNotification($modelNotification));
        });

        $hasDiscordPublic = $modelNotifications->contains(function (ModelNotification $notification) {
            return collect($notification->channels)->contains(NotificationChannel::DISCORD_PUBLIC);
        });

        if ($hasDiscordPublic && filled($recipients) && filled($modelNotifications)) {
            Notification::sendNow($recipients->first(), new NewModelNotification($modelNotifications->first()), [DiscordPublicChannel::class]);
        }
    }
}
