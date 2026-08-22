<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Listeners;

use App\Listeners\UpdateWebhookPostWithFailure;
use App\Models\WebhookLog;
use GuzzleHttp\Psr7\Response;
use Spatie\WebhookServer\Events\FinalWebhookCallFailedEvent;
use Tests\Feature\Tenant\TenantTestCase;

class UpdateWebhookPostWithFailureTest extends TenantTestCase
{
    public function test_handle_records_error_and_response_details_on_the_webhook_log(): void
    {
        $webhookLog = $this->createWebhookLog();

        $event = $this->makeEvent(
            meta: ['model_id' => $webhookLog->id],
            errorType: 'timeout',
            errorMessage: 'The request timed out.',
            response: new Response(500),
        );

        (new UpdateWebhookPostWithFailure)->handle($event);

        $properties = $webhookLog->fresh()->properties;

        $this->assertTrue($properties->contains('error_type', 'timeout'));
        $this->assertTrue($properties->contains('error_message', 'The request timed out.'));
        $this->assertTrue($properties->contains('status_code', 500));
        $this->assertTrue($properties->contains('reason_phrase', 'Internal Server Error'));
    }

    public function test_handle_records_only_error_details_when_no_response_is_present(): void
    {
        $webhookLog = $this->createWebhookLog();

        $event = $this->makeEvent(
            meta: ['model_id' => $webhookLog->id],
            errorType: 'connection',
            errorMessage: 'Could not connect.',
            response: null,
        );

        (new UpdateWebhookPostWithFailure)->handle($event);

        $properties = $webhookLog->fresh()->properties;

        $this->assertTrue($properties->contains('error_type', 'connection'));
        $this->assertFalse($properties->contains(fn (mixed $value): bool => is_array($value) && array_key_exists('status_code', $value)));
    }

    public function test_handle_does_nothing_when_the_webhook_log_cannot_be_found(): void
    {
        $event = $this->makeEvent(
            meta: ['model_id' => 999999],
            errorType: 'timeout',
            errorMessage: 'Missing log.',
            response: new Response(500),
        );

        (new UpdateWebhookPostWithFailure)->handle($event);

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

    /**
     * @param  array<string, int>  $meta
     */
    private function makeEvent(array $meta, string $errorType, string $errorMessage, ?Response $response): FinalWebhookCallFailedEvent
    {
        return new FinalWebhookCallFailedEvent(
            httpVerb: 'POST',
            webhookUrl: 'https://example.test/hook',
            payload: [],
            headers: [],
            meta: $meta,
            tags: [],
            attempt: 3,
            response: $response,
            errorType: $errorType,
            errorMessage: $errorMessage,
            uuid: 'test-uuid',
            transferStats: null,
        );
    }
}
