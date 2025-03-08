<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Enums\WebhookEvent;
use App\Models\Event;
use App\Models\Webhook;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class EventObserverTest extends TenantTestCase
{
    public function test_create_event_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::EVENT_CREATED],
        ])->create();

        Event::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_event_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::EVENT_UPDATED],
        ])->create();

        $event = Event::factory()->create();
        $event->update([
            'name' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_event_webhook_sent(): void
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::EVENT_DELETED],
        ])->create();

        $event = Event::factory()->create();
        $event->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
