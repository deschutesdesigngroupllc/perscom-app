<?php

namespace Tests\Feature\Tenant\Observers;

use Spatie\WebhookServer\CallWebhookJob;
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

        $award = AwardRecord::factory()->for($this->user)->create();

        Notification::assertSentTo($this->user, NewAwardRecord::class, function ($notification, $channels) use ($award) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($award->user);
            $mail->assertTo($award->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new award record has been added to your personnel file.', $nova->message);

            return true;
        });
    }

    public function test_create_award_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::AWARD_RECORD_CREATED],
        ])->create();

        AwardRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
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

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_award_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::AWARD_RECORD_DELETED],
        ])->create();

        $award = AwardRecord::factory()->create();
        $award->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
