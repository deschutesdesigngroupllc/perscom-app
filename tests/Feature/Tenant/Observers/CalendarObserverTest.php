<?php

namespace Tests\Feature\Tenant\Observers;

use App\Jobs\CallWebhook;
use App\Models\Calendar;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use Illuminate\Support\Facades\Queue;
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

        Queue::assertPushed(CallWebhook::class);
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

        Queue::assertPushed(CallWebhook::class);
    }

    public function test_delete_calendar_webhook_sent()
    {
        Queue::fake();

        Webhook::factory()->state([
            'events' => [WebhookEvent::CALENDAR_DELETED],
        ])->create();

        $calendar = Calendar::factory()->create();
        $calendar->delete();

        Queue::assertPushed(CallWebhook::class);
    }
}
