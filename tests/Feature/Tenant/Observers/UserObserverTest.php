<?php

namespace Tests\Feature\Tenant\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\User\AccountApproved;
use App\Notifications\User\AdminApprovalRequired;
use App\Notifications\User\ApprovalRequired;
use App\Notifications\User\PasswordChanged;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class UserObserverTest extends TenantTestCase
{
    public function test_user_assigned_appropriate_permissions()
    {
        $this->assertEquals($this->user->roles->pluck('id')->toArray(), setting('default_roles', []));
        $this->assertEquals($this->user->permissions->pluck('id')->toArray(), setting('default_permissions', []));
    }

    public function test_user_notes_updated_date_set()
    {
        $this->user->update(['notes' => 'foo bar']);

        $this->assertEqualsWithDelta(now(), $this->user->notes_updated_at, 1);
    }

    public function test_user_password_changed_notification_sent()
    {
        Notification::fake();

        $this->user->update([
            'password' => Str::password(),
        ]);

        Notification::assertSentTo($this->user, PasswordChanged::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            return true;
        });
    }

    public function test_user_approval_required_notification_sent()
    {
        Notification::fake();

        Cache::put('registration_admin_approval_required', true, 3600);

        $user = User::factory()->create();

        Notification::assertSentTo($user, ApprovalRequired::class, function ($notification, $channels) use ($user) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($user);
            $mail->assertTo($user->email);

            return true;
        });
    }

    public function test_admin_approval_required_notification_sent()
    {
        Notification::fake();

        Cache::put('registration_admin_approval_required', true, 3600);

        $this->user->assignRole('Admin');

        User::factory()->create();

        Notification::assertSentTo($this->user, AdminApprovalRequired::class, function ($notification, $channels) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($this->user);
            $mail->assertTo($this->user->email);

            return true;
        });
    }

    public function test_account_approved_notification_sent()
    {
        Notification::fake();

        Cache::put('registration_admin_approval_required', true, 3600);

        $user = User::factory()->create();
        $user->update([
            'approved' => true,
        ]);

        Notification::assertSentTo($user, AccountApproved::class, function ($notification, $channels) use ($user) {
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
