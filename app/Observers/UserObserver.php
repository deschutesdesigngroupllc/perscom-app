<?php

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\User\PasswordChanged;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @return void
     */
    public function created(User $user)
    {
        $user->assignRole(setting('default_roles'));
        $user->givePermissionTo(setting('default_permissions'));

        Webhook::query()->whereJsonContains('events', [WebhookEvent::USER_CREATED->value])->each(function (Webhook $webhook) use ($user) {
            WebhookService::dispatch($webhook, WebhookEvent::USER_CREATED->value, $user);
        });
    }

    /**
     * Handle the User "updated" event.
     *
     * @return void
     */
    public function updated(User $user)
    {
        if ($user->isDirty('notes')) {
            $user->updateQuietly([
                'notes_updated_at' => now(),
            ]);
        }

        if ($user->isDirty('password')) {
            Notification::send($user, new PasswordChanged());
        }

        Webhook::query()->whereJsonContains('events', [WebhookEvent::USER_UPDATED->value])->each(function (Webhook $webhook) use ($user) {
            WebhookService::dispatch($webhook, WebhookEvent::USER_UPDATED->value, $user);
        });
    }

    /**
     * Handle the User "deleted" event.
     *
     * @return void
     */
    public function deleted(User $user)
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::USER_DELETED->value])->each(function (Webhook $webhook) use ($user) {
            WebhookService::dispatch($webhook, WebhookEvent::USER_DELETED->value, $user);
        });
    }

    /**
     * Handle the User "restored" event.
     *
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
