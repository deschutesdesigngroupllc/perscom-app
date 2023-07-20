<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Jobs\GenerateOpenAiNewsfeedContent;
use App\Models\Enums\WebhookEvent;
use App\Models\ServiceRecord;
use App\Models\Webhook;
use App\Notifications\Tenant\NewServiceRecord;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\Feature\Tenant\TenantTestCase;

class ServiceRecordObserverTest extends TenantTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake([GenerateOpenAiNewsfeedContent::class]);
    }

    public function test_create_service_record_notification_sent()
    {
        Notification::fake();

        $service = ServiceRecord::factory()->create();

        Notification::assertSentTo($this->user, NewServiceRecord::class, function ($notification, $channels) use ($service) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($service->user);
            $mail->assertTo($service->user->email);

            $nova = $notification->toNova();
            $this->assertSame('A new service record has been added to your personnel file.', $nova->message);

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

        Queue::assertPushed(CallWebhook::class);
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

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_service_record_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::SERVICE_RECORD_DELETED],
        ])->create();

        $service = ServiceRecord::factory()->create();
        $service->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
