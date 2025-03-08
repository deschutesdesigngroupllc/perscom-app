<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\RankRecord;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewRankRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class RankRecordObserverTest extends TenantTestCase
{
    public function test_create_rank_record_assigns_user_rank(): void
    {
        $rank = RankRecord::factory()->for($user = User::factory()->create())->create();

        $this->assertSame($rank->rank->getKey(), $user->fresh()->rank->getKey());
    }

    public function test_create_rank_record_notification_sent(): void
    {
        Notification::fake();

        $rank = RankRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewRankRecord::class, function (NewRankRecord $notification, $channels) use ($rank): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($rank->user);
            $mail->assertTo($rank->user->email);

            return true;
        });
    }

    public function test_create_rank_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::RANK_RECORD_CREATED],
        ])->create();

        RankRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_rank_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::RANK_RECORD_UPDATED],
        ])->create();

        $rank = RankRecord::factory()->create();
        $rank->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_rank_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::RANK_RECORD_DELETED],
        ])->create();

        $rank = RankRecord::factory()->create();
        $rank->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
