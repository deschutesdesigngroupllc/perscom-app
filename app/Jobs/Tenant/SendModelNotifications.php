<?php

declare(strict_types=1);

namespace App\Jobs\Tenant;

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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class SendModelNotifications implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public function __construct(protected SendsModelNotifications|Model $model, protected string $event)
    {
        $this->afterCommit = true;
    }

    public function handle(): void
    {
        $this->model->loadMissing('modelNotifications');

        // @phpstan-ignore-next-line
        if (blank($this->model->modelNotifications)) {
            return;
        }

        $recipients = collect();

        $modelNotifications = $this->model->modelNotifications()->where('event', $this->event)->get();
        $modelNotifications->each(function (ModelNotification $modelNotification) use ($recipients): void {
            $notificationRecipients = $modelNotification->getRecipients();
            if (filled($notificationRecipients)) {
                $notificationRecipients->each(function (User $user) use ($modelNotification, $recipients): void {
                    $recipients->put($user->getKey(), $modelNotification);
                });
            }
        });

        $recipients->each(function (ModelNotification $modelNotification, $userId): void {
            $user = User::findOrFail($userId);
            $user->notify(new NewModelNotification($modelNotification));
        });

        $hasDiscordPublic = $modelNotifications->contains(fn (ModelNotification $notification) => Collection::wrap($notification->channels)->contains(NotificationChannel::DISCORD_PUBLIC));

        if ($hasDiscordPublic && filled($recipients) && filled($modelNotifications)) {
            Notification::sendNow($recipients->first(), new NewModelNotification($modelNotifications->first()), [DiscordPublicChannel::class]);
        }
    }
}
