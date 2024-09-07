<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\User\AccountApproved;
use App\Notifications\User\AdminApprovalRequired;
use App\Notifications\User\ApprovalRequired;
use App\Notifications\User\PasswordChanged;
use App\Services\WebhookService;
use App\Settings\PermissionSettings;
use App\Settings\RegistrationSettings;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    public function created(User $user): void
    {
        /** @var PermissionSettings $permissionSettings */
        $permissionSettings = app(PermissionSettings::class);

        $user->assignRole($permissionSettings->default_roles);
        $user->givePermissionTo($permissionSettings->default_permissions);

        Webhook::query()->whereJsonContains('events', [WebhookEvent::USER_CREATED->value])->each(function (Webhook $webhook) use ($user) {
            WebhookService::dispatch($webhook, WebhookEvent::USER_CREATED->value, $user);
        });

        /** @var RegistrationSettings $registrationSettings */
        $registrationSettings = app(RegistrationSettings::class);
        if ($registrationSettings->admin_approval_required) {
            $user->updateQuietly([
                'approved' => false,
            ]);

            Notification::send($user, new ApprovalRequired);
            Notification::send(User::role('Admin')->get(), new AdminApprovalRequired($user));
        }
    }

    public function updated(User $user): void
    {
        if ($user->isDirty('notes')) {
            $user->updateQuietly([
                'notes_updated_at' => now(),
            ]);
        }

        if ($user->approved && $user->isDirty('approved')) {
            Notification::send($user, new AccountApproved);
        }

        if ($user->isDirty('password')) {
            Notification::send($user, new PasswordChanged);
        }

        Webhook::query()->whereJsonContains('events', [WebhookEvent::USER_UPDATED->value])->each(function (Webhook $webhook) use ($user) {
            WebhookService::dispatch($webhook, WebhookEvent::USER_UPDATED->value, $user);
        });
    }

    public function deleted(User $user): void
    {
        Webhook::query()->whereJsonContains('events', [WebhookEvent::USER_DELETED->value])->each(function (Webhook $webhook) use ($user) {
            WebhookService::dispatch($webhook, WebhookEvent::USER_DELETED->value, $user);
        });
    }
}
