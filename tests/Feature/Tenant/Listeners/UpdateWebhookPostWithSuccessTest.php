<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\UpdateWebhookPostWithSuccess;
use App\Models\WebhookLog;
use GuzzleHttp\Psr7\Response;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;
use Tests\Feature\Tenant\TenantTestCase;

class UpdateWebhookPostWithSuccessTest extends TenantTestCase
{
    public function test_handle_records_the_response_status_on_the_webhook_log(): void
    {
        $webhookLog = $this->createWebhookLog();

        $event = $this->makeEvent(
            meta: ['model_id' => $webhookLog->id],
            response: new Response(200),
        );

        (new UpdateWebhookPostWithSuccess)->handle($event);

        $properties = $webhookLog->fresh()->properties;

        $this->assertSame(200, $properties->get('status_code'));
        $this->assertSame('OK', $properties->get('reason_phrase'));
    }

    public function test_handle_does_nothing_when_the_webhook_log_cannot_be_found(): void
    {
        $event = $this->makeEvent(
            meta: ['model_id' => 999999],
            response: new Response(200),
        );

        (new UpdateWebhookPostWithSuccess)->handle($event);

        $this->assertSame(0, WebhookLog::query()->count());
    }

    private function createWebhookLog(): WebhookLog
    {
        $webhookLog = new WebhookLog;
        $webhookLog->log_name = 'webhook';
        $webhookLog->description = ['message' => 'webhook call'];
        $webhookLog->properties = collect();
        $webhookLog->save();

        return $webhookLog;
    }

    private function makeEvent(array $meta, ?Response $response): WebhookCallSucceededEvent
    {
        return new WebhookCallSucceededEvent(
            httpVerb: 'POST',
            webhookUrl: 'https://example.test/hook',
            payload: [],
            headers: [],
            meta: $meta,
            tags: [],
            attempt: 1,
            response: $response,
            errorType: null,
            errorMessage: null,
            uuid: 'test-uuid',
            transferStats: null,
        );
    }
}
