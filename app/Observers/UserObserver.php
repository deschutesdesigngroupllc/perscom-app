<?php

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\User\AccountApproved;
use App\Notifications\User\AdminApprovalRequired;
use App\Notifications\User\ApprovalRequired;
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

        if (setting('registration_admin_approval_required', false)) {
            $user->updateQuietly([
                'approved' => false,
            ]);

            Notification::send($user, new ApprovalRequired());
            Notification::send(User::role('Admin')->get(), new AdminApprovalRequired($user));
        }
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

        if ($user->approved && $user->isDirty('approved')) {
            Notification::send($user, new AccountApproved());
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
}
