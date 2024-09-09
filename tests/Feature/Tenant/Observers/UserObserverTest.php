<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\User\AccountApproved;
use App\Notifications\User\AdminApprovalRequired;
use App\Notifications\User\ApprovalRequired;
use App\Notifications\User\PasswordChanged;
use App\Settings\PermissionSettings;
use App\Settings\RegistrationSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class UserObserverTest extends TenantTestCase
{
    public function test_user_assigned_appropriate_permissions()
    {
        PermissionSettings::fake([
            'default_roles' => [1],
            'default_permissions' => [1],
        ]);

        /** @var PermissionSettings $settings */
        $settings = app(PermissionSettings::class);

        $user = User::factory()->create();

        $this->assertEquals($user->roles->pluck('id')->toArray(), $settings->default_roles);
        $this->assertEquals($user->permissions->pluck('id')->toArray(), $settings->default_permissions);
    }

    public function test_user_notes_updated_date_set()
    {
        $user = User::factory()->create();

        $user->update(['notes' => 'foo bar']);

        $this->assertEqualsWithDelta(now(), $user->notes_updated_at, 3);
    }

    public function test_user_password_changed_notification_sent()
    {
        Notification::fake();

        $user = User::factory()->create();

        $user->update([
            'password' => Str::password(),
        ]);

        Notification::assertSentTo($user, PasswordChanged::class, function (PasswordChanged $notification, $channels) use ($user) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($user);
            $mail->assertTo($user->email);

            return true;
        });
    }

    public function test_user_approval_required_notification_sent()
    {
        Notification::fake();

        RegistrationSettings::fake([
            'admin_approval_required' => true,
        ]);

        $user = User::factory()->create();

        Notification::assertSentTo($user, ApprovalRequired::class, function (ApprovalRequired $notification, $channels) use ($user) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($user);
            $mail->assertTo($user->email);

            return true;
        });
    }

    public function test_admin_approval_required_notification_sent()
    {
        Notification::fake();

        RegistrationSettings::fake([
            'admin_approval_required' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole(Utils::getSuperAdminName());

        User::factory()->create();

        Notification::assertSentTo($user, AdminApprovalRequired::class, function (AdminApprovalRequired $notification, $channels) use ($user) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($user);
            $mail->assertTo($user->email);

            return true;
        });
    }

    public function test_account_approved_notification_sent()
    {
        Notification::fake();

        RegistrationSettings::fake([
            'admin_approval_required' => true,
        ]);

        $user = User::factory()->create();
        $user->update([
            'approved' => true,
        ]);

        Notification::assertSentTo($user, AccountApproved::class, function (AccountApproved $notification, $channels) use ($user) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($user);
            $mail->assertTo($user->email);

            return true;
        });
    }

    public function test_create_user_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::USER_CREATED],
        ])->create();

        User::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_user_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::USER_UPDATED],
        ])->create();

        $user = User::factory()->create();
        $user->update([
            'name' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_user_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::USER_DELETED],
        ])->create();

        $user = User::factory()->create();
        $user->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
