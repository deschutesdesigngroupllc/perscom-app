<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\Enums\WebhookEvent;
use App\Models\QualificationRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewQualificationRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class QualificationRecordObserverTest extends TenantTestCase
{
    public function test_create_qualification_record_notification_sent()
    {
        Notification::fake();

        $qualification = QualificationRecord::factory()->create();

        Notification::assertSentTo($qualification->user, NewQualificationRecord::class);
    }

    public function test_create_qualification_record_webhook_sent()
    {
        Queue::fake();

        $webhook = Webhook::factory()->state([
            'events' => [WebhookEvent::QUALIFICATION_RECORD_CREATED],
        ])->create();

        QualificationRecord::factory()->create();

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_update_qualification_record_webhook_sent()
    {
        Queue::fake();

        $webhook = Webhook::factory()->state([
            'events' => [WebhookEvent::QUALIFICATION_RECORD_UPDATED],
        ])->create();

        $qualification = QualificationRecord::factory()->create();
        $qualification->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_qualification_record_webhook_sent()
    {
        Queue::fake();

        $webhook = Webhook::factory()->state([
            'events' => [WebhookEvent::QUALIFICATION_RECORD_DELETED],
        ])->create();

        $qualification = QualificationRecord::factory()->create();
        $qualification->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
