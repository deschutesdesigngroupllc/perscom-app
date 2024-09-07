<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Webhook;
use App\Notifications\Tenant\NewServiceRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class ServiceRecordObserverTest extends TenantTestCase
{
    public function test_create_service_record_notification_sent()
    {
        Notification::fake();

        $service = ServiceRecord::factory()->for($user = User::factory()->create())->create();

        Notification::assertSentTo($user, NewServiceRecord::class, function (NewServiceRecord $notification, $channels) use ($service) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($service->user);
            $mail->assertTo($service->user->email);

            return true;
        });
    }

    public function test_create_service_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SERVICE_RECORD_CREATED],
        ])->create();

        ServiceRecord::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_service_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SERVICE_RECORD_UPDATED],
        ])->create();

        $service = ServiceRecord::factory()->create();
        $service->update([
            'text' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_service_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SERVICE_RECORD_DELETED],
        ])->create();

        $service = ServiceRecord::factory()->create();
        $service->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
