<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\User\PasswordChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
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

        Notification::assertSentTo($this->user, PasswordChanged::class);
    }

    public function test_create_user_webhook_sent()
    {
        Queue::fake();

        $webhook = Webhook::factory()->state([
            'events' => [WebhookEvent::USER_CREATED],
        ])->create();

        User::factory()->create();

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_update_user_webhook_sent()
    {
        Queue::fake();

        $webhook = Webhook::factory()->state([
            'events' => [WebhookEvent::USER_UPDATED],
        ])->create();

        $user = User::factory()->create();
        $user->update([
            'name' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_user_webhook_sent()
    {
        Queue::fake();

        $webhook = Webhook::factory()->state([
            'events' => [WebhookEvent::USER_DELETED],
        ])->create();

        $user = User::factory()->create();
        $user->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
