<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\AwardRecord;
use App\Models\Enums\WebhookEvent;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewAwardRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class AwardRecordObserverTest extends TenantTestCase
{
    public function test_create_award_record_notification_sent()
    {
        Notification::fake();

        $award = AwardRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewAwardRecord::class, function (NewAwardRecord $notification, $channels) use ($award) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($award->user);
            $mail->assertTo($award->user->email);

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
