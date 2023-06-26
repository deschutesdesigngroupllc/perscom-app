<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\AwardRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAwardRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class AwardRecordObserverTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_create_award_record_notification_sent()
    {
        Notification::fake();

        $award = AwardRecord::factory()->create();

        Notification::assertSentTo($award->user, NewAwardRecord::class);
    }

    public function test_create_award_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::AWARD_RECORD_CREATED],
        ])->create();

        AwardRecord::factory()->create();

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_update_award_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::AWARD_RECORD_UPDATED],
        ])->create();

        $award = AwardRecord::factory()->create();
        $award->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_award_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::AWARD_RECORD_DELETED],
        ])->create();

        $award = AwardRecord::factory()->create();
        $award->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
