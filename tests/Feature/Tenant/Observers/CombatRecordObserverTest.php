<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\CombatRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewCombatRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class CombatRecordObserverTest extends TenantTestCase
{
    public function test_create_combat_record_notification_sent()
    {
        Notification::fake();

        $combat = CombatRecord::factory()->create();

        Notification::assertSentTo($combat->user, NewCombatRecord::class);
    }

    public function test_create_combat_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::COMBAT_RECORD_CREATED],
        ])->create();

        CombatRecord::factory()->create();

        Queue::assertPushed(CallWebhook::class);
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

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_combat_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::COMBAT_RECORD_DELETED],
        ])->create();

        $combat = CombatRecord::factory()->create();
        $combat->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
