<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Models\Award;
use App\Models\Enums\WebhookEvent;
use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class WebhookServiceTest extends TenantTestCase
{
    public function test_it_dispatches_a_webhook()
    {
        Queue::fake();

        $webhook = Webhook::factory()->create();
        $award = Award::factory()->create();

        WebhookService::dispatch($webhook, WebhookEvent::EVENT_CREATED->value, $award);

        Queue::assertPushed(CallWebhookJob::class);
    }
}
