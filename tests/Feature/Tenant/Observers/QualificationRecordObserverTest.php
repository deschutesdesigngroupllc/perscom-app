<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\QualificationRecord;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewQualificationRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class QualificationRecordObserverTest extends TenantTestCase
{
    public function test_create_qualification_record_notification_sent(): void
    {
        Notification::fake();

        $qualification = QualificationRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewQualificationRecord::class, function (NewQualificationRecord $notification, iterable $channels) use ($qualification): true {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($qualification->user);
            $mail->assertTo($qualification->user->email);

            return true;
        });
    }

    public function test_create_qualification_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::QUALIFICATION_RECORD_CREATED],
        ])->create();

        QualificationRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_qualification_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::QUALIFICATION_RECORD_UPDATED],
        ])->create();

        $qualification = QualificationRecord::factory()->create();
        $qualification->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_qualification_record_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::QUALIFICATION_RECORD_DELETED],
        ])->create();

        $qualification = QualificationRecord::factory()->create();
        $qualification->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
