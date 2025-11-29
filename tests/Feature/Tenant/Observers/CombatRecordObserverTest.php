<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

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
    public function test_create_combat_record_notification_sent(): void
    {
        Notification::fake();

        $combat = CombatRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewCombatRecord::class, function (NewCombatRecord $notification, iterable $channels) use ($combat): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($combat->user);
            $mail->assertTo($combat->user->email);

            return true;
        });
    }

    public function test_create_combat_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::COMBAT_RECORD_CREATED],
        ])->create();

        CombatRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_combat_record_webhook_sent(): void
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

    public function test_delete_combat_record_webhook_sent(): void
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
