<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Observers;

use App\Models\Calendar;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class CalendarObserverTest extends TenantTestCase
{
    public function test_create_calendar_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::CALENDAR_CREATED],
        ])->create();

        Calendar::factory()->create();

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_update_calendar_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::CALENDAR_UPDATED],
        ])->create();

        $calendar = Calendar::factory()->create();
        $calendar->update([
            'name' => 'foo bar',
        ]);

        Queue::assertPushed(CallWebhookJob::class);
    }

    public function test_delete_calendar_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::CALENDAR_DELETED],
        ])->create();

        $calendar = Calendar::factory()->create();
        $calendar->delete();

        Queue::assertPushed(CallWebhookJob::class);
    }
}
