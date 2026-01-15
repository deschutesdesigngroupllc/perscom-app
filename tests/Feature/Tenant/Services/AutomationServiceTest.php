<?php

declare(strict_types=1);

namespace Tests\Feature\Tenant\Services;

use App\Events\Automations\UserCreated;
use App\Models\Automation;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\MessageChannel;
use App\Models\Message;
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

        $webhook = Webhook::factory()->create();
        $automation = Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
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

        $webhook = Webhook::factory()->create();
        Automation::factory()
            ->webhookAction()
            ->disabled()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
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

        $webhook = Webhook::factory()->create();
        $automation = Automation::factory()
            ->webhookAction()
            ->withCondition('model.status == "inactive"')
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        Queue::assertNothingPushed();

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'status' => AutomationLogStatus::CONDITION_FAILED->value,
            'condition_result' => false,
        ]);
    }

    public function test_it_evaluates_condition_and_executes_on_true(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->create();
        $automation = Automation::factory()
            ->webhookAction()
            ->withCondition('filled(model.name)')
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create(['name' => 'Test User']);

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
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

        $webhook1 = Webhook::factory()->create(['url' => 'https://first.example.com']);
        $webhook2 = Webhook::factory()->create(['url' => 'https://second.example.com']);

        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'name' => 'Second',
                'webhook_id' => $webhook2->id,
                'priority' => 10,
            ]);

        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'name' => 'First',
                'webhook_id' => $webhook1->id,
                'priority' => 0,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class, 2);

        $logs = AutomationLog::query()->orderBy('created_at')->get();
        $this->assertEquals('First', $logs->first()->automation->name);
    }

    public function test_it_logs_execution_time(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->create();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertNotNull($log->execution_time_ms);
        $this->assertGreaterThanOrEqual(0, $log->execution_time_ms);
    }

    public function test_it_stores_context_in_log(): void
    {
        Queue::fake();

        $webhook = Webhook::factory()->create();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create(['name' => 'Test User']);

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
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

        $webhook = Webhook::factory()->create();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_DELETED)
            ->create([
                'webhook_id' => $webhook->id,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
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

        $webhook = Webhook::factory()->create();
        Automation::factory()
            ->webhookAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'webhook_id' => $webhook->id,
                'webhook_payload_template' => [
                    'user_id' => '{{ model.id }}',
                    'user_name' => '{{ model.name }}',
                    'custom_field' => 'static_value',
                ],
            ]);

        $user = User::factory()->create(['name' => 'Test User']);

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        Queue::assertPushed(CallWebhookJob::class);

        $log = AutomationLog::query()->first();
        $this->assertTrue($log->action_payload['custom_payload']);
    }

    public function test_it_processes_automation_with_message_action(): void
    {
        $sourceMessage = Message::factory()->create([
            'message' => 'Welcome to the team!',
            'channels' => collect([MessageChannel::DATABASE]),
        ]);

        $automation = Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'message_id' => $sourceMessage->id,
            ]);

        $user = User::factory()->create(['name' => 'New User']);

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        $this->assertDatabaseHas('automations_logs', [
            'automation_id' => $automation->id,
            'trigger' => AutomationTrigger::USER_CREATED->value,
            'status' => AutomationLogStatus::EXECUTED->value,
        ]);

        // A new message should have been created
        $this->assertDatabaseCount('messages', 2);
    }

    public function test_it_uses_message_template_override(): void
    {
        $sourceMessage = Message::factory()->create([
            'message' => 'Original message content',
            'channels' => collect([MessageChannel::DATABASE]),
        ]);

        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'message_id' => $sourceMessage->id,
                'message_template' => 'Hello {{ model.name }}, welcome!',
            ]);

        $user = User::factory()->create(['name' => 'John Doe']);

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertEquals('Hello John Doe, welcome!', $log->action_payload['message_content']);
    }

    public function test_it_uses_channels_from_source_message(): void
    {
        $sourceMessage = Message::factory()->create([
            'message' => 'Test message',
            'channels' => collect([MessageChannel::DATABASE, MessageChannel::MAIL]),
        ]);

        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'message_id' => $sourceMessage->id,
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertCount(2, $log->action_payload['channels']);
        $this->assertContains(MessageChannel::DATABASE->value, $log->action_payload['channels']);
        $this->assertContains(MessageChannel::MAIL->value, $log->action_payload['channels']);
    }

    public function test_it_evaluates_recipients_expression(): void
    {
        $sourceMessage = Message::factory()->create([
            'message' => 'Test message',
            'channels' => collect([MessageChannel::DATABASE]),
        ]);

        Automation::factory()
            ->messageAction()
            ->forTrigger(AutomationTrigger::USER_CREATED)
            ->create([
                'message_id' => $sourceMessage->id,
                'message_recipients_expression' => '[model.id]',
            ]);

        $user = User::factory()->create();

        $event = new UserCreated(
            model: $user,
            causer: $user,
            trigger: AutomationTrigger::USER_CREATED,
        );

        $this->service->process($event);

        $log = AutomationLog::query()->first();
        $this->assertContains($user->id, $log->action_payload['recipients']);
    }

    public function test_test_condition_validates_valid_expression(): void
    {
        $result = $this->service->testCondition('model.status == "active"', [
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
}
