<?php

namespace Tests\Feature\Tenant\Observers;

use Spatie\WebhookServer\CallWebhookJob;
use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\Enums\WebhookEvent;
use App\Models\RankRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewRankRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class RankRecordObserverTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_create_rank_record_assigns_user_rank(): void
    {
        $this->assertNull($this->user->rank);

        $rank = RankRecord::factory()->for($this->user)->create();

        $user = $this->user->fresh();

        $this->assertSame($rank->rank->getKey(), $user->rank->getKey());
    }

    public function test_create_rank_record_notification_sent()
    {
        Notification::fake();

        $rank = RankRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewRankRecord::class, function ($notification, $channels) use ($rank) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($rank->user);
            $mail->assertTo($rank->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new rank record has been added to your personnel file.', $nova->message);

            return true;
        });
    }

    public function test_create_rank_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::RANK_RECORD_CREATED],
        ])->create();

        RankRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_rank_record_webhook_sent()
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

    public function test_delete_rank_record_webhook_sent()
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
