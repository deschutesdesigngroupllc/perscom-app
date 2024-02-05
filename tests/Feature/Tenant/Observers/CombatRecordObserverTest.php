<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\CombatRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewCombatRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class CombatRecordObserverTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_create_combat_record_notification_sent()
    {
        Notification::fake();

        $combat = CombatRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewCombatRecord::class, function ($notification, $channels) use ($combat) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($combat->user);
            $mail->assertTo($combat->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new combat record has been added to your personnel file.', $nova->message);

            return true;
        });
    }

    public function test_create_combat_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::COMBAT_RECORD_CREATED],
        ])->create();

        CombatRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_combat_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::COMBAT_RECORD_UPDATED],
        ])->create();

        $combat = CombatRecord::factory()->create();
        $combat->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_combat_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::COMBAT_RECORD_DELETED],
        ])->create();

        $combat = CombatRecord::factory()->create();
        $combat->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
