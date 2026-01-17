<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Events\Automations\UserCreated;
use App\Models\Automation;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\NotificationChannel;
use App\Models\User;
use App\Models\Webhook;
use App\Services\AutomationService;
use Illuminate\Support\Facades\Queue;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\Feature\Tenant\TenantTestCase;

class AutomationServiceTest extends TenantTestCase
{
    private AutomationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(AutomationService::class);
    }

    public function test_it_processes_automation_with_webhook_action(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        $automation = Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'trigger' => AutomationTrigger::USER_CREATED->value,
            'status' => AutomationLogStatus::EXECUTED->value,
        ]);
    }

    public function test_it_skips_disabled_automations(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        Automation::factory()
            ->webhookAction()
            ->disabled()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertNothingPushed();

        $this->assertDatabaseMissing('automations_logs', [
            'trigger' => AutomationTrigger::USER_CREATED->value,
        ]);
    }

    public function test_it_evaluates_condition_and_skips_on_false(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        $automation = Automation::factory()
            ->webhookAction()
            ->withCondition('model["name"] == "Non-Existent User"')
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly(['name' => 'Test User']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertNotPushed(CallWebhookJob::class);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'status' => AutomationLogStatus::CONDITION_FAILED->value,
            'condition_result' => false,
        ]);
    }

    public function test_it_evaluates_condition_and_executes_on_true(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        $automation = Automation::factory()
            ->webhookAction()
            ->withCondition('filled(model["name"])')
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly(['name' => 'Test User']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'status' => AutomationLogStatus::EXECUTED->value,
            'condition_result' => true,
        ]);
    }

    public function test_it_respects_automation_priority(): void
    {
        Queue::fake();

        $webhook1 = Webhook::factory()->createQuietly(['url' => 'https://first.example.com']);
        $webhook2 = Webhook::factory()->createQuietly(['url' => 'https://second.example.com']);

        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'name' => 'HighPriority',
                'webhook_id' => $webhook1->id,
                'priority' => 10,
            ]);

        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'name' => 'LowPriority',
                'webhook_id' => $webhook2->id,
                'priority' => 0,
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class, 2);

        $logs = AutomationLog::query()->orderBy('created_at')->get();
        $this->assertEquals('HighPriority', $logs->first()->automation->name);
    }

    public function test_it_logs_execution_time(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertNotNull($log->execution_time_ms);
        $this->assertGreaterThanOrEqual(0, $log->execution_time_ms);
    }

    public function test_it_stores_context_in_log(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly(['name' => 'Test User']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertIsArray($log->context);
        $this->assertArrayHasKey('model', $log->context);
        $this->assertEquals('Test User', $log->context['model']['name']);
    }

    public function test_it_only_triggers_for_matching_events(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_DELETED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertNothingPushed();

        $this->assertDatabaseMissing('automations_logs', [
            'trigger' => AutomationTrigger::USER_CREATED->value,
        ]);
    }

    public function test_it_uses_custom_webhook_payload_template(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
                'webhook_payload_template' => [
                    'user_id' => '{{ model.id }}',
                    'user_name' => '{{ model.name }}',
                    'custom_field' => 'static_value',
                ],
            ]);

        $user = User::factory()->createQuietly(['name' => 'Test User']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class);

        $log = AutomationLog::query()->first();
        $this->assertTrue($log->action_payload['custom_payload']);
    }

    public function test_it_processes_automation_with_message_action(): void
    {
        $automation = Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'message_channels' => [NotificationChannel::DATABASE],
                'message_content' => 'Welcome to the team!',
            ]);

        $user = User::factory()->createQuietly(['name' => 'New User']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'trigger' => AutomationTrigger::USER_CREATED->value,
            'status' => AutomationLogStatus::EXECUTED->value,
        ]);

        // A new message should have been created
        $this->assertDatabaseCount('messages', 1);
    }

    public function test_it_uses_twig_templating_in_message_content(): void
    {
        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'message_channels' => [NotificationChannel::DATABASE],
                'message_content' => 'Hello {{ model.name }}, welcome!',
            ]);

        $user = User::factory()->createQuietly(['name' => 'John Doe']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertEquals('Hello John Doe, welcome!', $log->action_payload['message_content']);
    }

    public function test_it_uses_configured_channels(): void
    {
        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'message_channels' => [NotificationChannel::DATABASE, NotificationChannel::MAIL],
                'message_content' => 'Test message',
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertCount(2, $log->action_payload['channels']);
        $this->assertContains(NotificationChannel::DATABASE->value, $log->action_payload['channels']);
        $this->assertContains(NotificationChannel::MAIL->value, $log->action_payload['channels']);
    }

    public function test_it_evaluates_recipients_expression(): void
    {
        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'message_channels' => [NotificationChannel::DATABASE],
                'message_content' => 'Test message',
                'message_recipients_expression' => '[model["id"]]',
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertContains($user->id, $log->action_payload['recipients']);
    }

    public function test_test_condition_validates_valid_expression(): void
    {
        $result = $this->service->testCondition('model["status"] == "active"', [
            'model' => ['status' => 'active'],
        ]);

        $this->assertTrue($result['valid']);
        $this->assertTrue($result['result']);
        $this->assertNull($result['error']);
    }

    public function test_test_condition_returns_error_for_invalid_expression(): void
    {
        $result = $this->service->testCondition('invalid..syntax', [
            'model' => ['status' => 'active'],
        ]);

        $this->assertFalse($result['valid']);
        $this->assertNull($result['result']);
        $this->assertNotNull($result['error']);
    }

    public function test_preview_webhook_payload_with_valid_template(): void
    {
        $template = json_encode([
            'user_id' => '{{ model.id }}',
            'user_name' => '{{ model.name }}',
        ]);

        $result = $this->service->previewWebhookPayload($template, [
            'model' => ['id' => 123, 'name' => 'Test User'],
        ]);

        $this->assertTrue($result['valid']);
        $this->assertNull($result['error']);
        $this->assertEquals('123', $result['result']['user_id']);
        $this->assertEquals('Test User', $result['result']['user_name']);
    }

    public function test_preview_webhook_payload_with_invalid_json(): void
    {
        $result = $this->service->previewWebhookPayload('not valid json', [
            'model' => ['id' => 123],
        ]);

        $this->assertFalse($result['valid']);
        $this->assertNull($result['result']);
        $this->assertEquals('Invalid JSON format', $result['error']);
    }

    public function test_preview_message_template_with_valid_template(): void
    {
        $result = $this->service->previewMessageTemplate('Hello {{ model.name }}!', [
            'model' => ['name' => 'John'],
        ]);

        $this->assertTrue($result['valid']);
        $this->assertNull($result['error']);
        $this->assertEquals('Hello John!', $result['result']);
    }

    public function test_preview_message_template_with_invalid_syntax(): void
    {
        $result = $this->service->previewMessageTemplate('Hello {{ model.name', [
            'model' => ['name' => 'John'],
        ]);

        $this->assertFalse($result['valid']);
        $this->assertNull($result['result']);
        $this->assertNotNull($result['error']);
    }

    public function test_it_supports_twig_filters_in_webhook_payload(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->createQuietly();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
                'webhook_payload_template' => [
                    'upper_name' => '{{ model.name | upper }}',
                    'lower_name' => '{{ model.name | lower }}',
                    'stripped' => '{{ model.bio | striptags }}',
                ],
            ]);

        $user = User::factory()->createQuietly([
            'name' => 'John Doe',
            'bio' => '<p>Hello <strong>World</strong></p>',
        ]);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class);

        $log = AutomationLog::query()->first();
        $this->assertEquals(AutomationLogStatus::EXECUTED, $log->status);
    }

    public function test_it_supports_twig_filters_in_message_template(): void
    {
        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'message_channels' => [NotificationChannel::DATABASE],
                'message_content' => 'Hello {{ model.name | upper }}, welcome!',
            ]);

        $user = User::factory()->createQuietly(['name' => 'John Doe']);

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertEquals('Hello JOHN DOE, welcome!', $log->action_payload['message_content']);
    }

    public function test_it_handles_missing_webhook_gracefully(): void
    {
        $webhook = Webhook::factory()->createQuietly();
        $automation = Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'webhook_id' => $webhook->id,
            ]);

        $webhook->delete();

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'status' => AutomationLogStatus::FAILED->value,
        ]);
    }

    public function test_it_handles_missing_message_configuration_gracefully(): void
    {
        $automation = Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->createQuietly([
                'message_channels' => null,
                'message_content' => null,
            ]);

        $user = User::factory()->createQuietly();

        $event = new UserCreated(
            subject: $user,
            changedAttributes: [],
        );

        $this->service->process($event);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'status' => AutomationLogStatus::FAILED->value,
        ]);
    }
}
