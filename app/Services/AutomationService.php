<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AutomationTriggerable;
use App\Data\Automations\AutomationResultData;
use App\Exceptions\ExpressionEvaluationException;
use App\Models\Automation;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use stdClass;
use Throwable;
use Twig\Environment;

class AutomationService
{
    public function __construct(
        protected ExpressionLanguageService $expressionLanguageService,
        protected Environment $twig,
    ) {}

    /**
     * Process an automation-triggerable event.
     *
     * @return Collection<int, AutomationResultData>
     */
    public function process(AutomationTriggerable $event): Collection
    {
        $automations = Automation::query()
            ->enabled()
            ->forTrigger($event->getTriggerType())
            ->orderBy('priority', 'desc')
            ->get();

        return $automations->map(fn (Automation $automation): AutomationResultData => $this->executeAutomation($automation, $event));
    }

    /**
     * Execute a single automation.
     */
    public function executeAutomation(Automation $automation, AutomationTriggerable $event): AutomationResultData
    {
        $startTime = microtime(true);
        $context = $event->getExpressionContext();

        try {
            // Evaluate condition if present
            $conditionResult = true;
            if (filled($automation->condition)) {
                $conditionResult = (bool) $this->expressionLanguageService->evaluate(
                    $automation->condition,
                    $context
                );
            }

            // If condition failed, log and return
            if (! $conditionResult) {
                $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);

                $log = $this->createLog($automation, $event, $context, [
                    'status' => AutomationLogStatus::CONDITION_FAILED,
                    'condition_expression' => $automation->condition,
                    'condition_result' => false,
                    'execution_time_ms' => $executionTimeMs,
                ]);

                return new AutomationResultData(
                    status: AutomationLogStatus::CONDITION_FAILED,
                    log: $log,
                    executionTimeMs: $executionTimeMs,
                );
            }

            // Execute the appropriate action
            $actionPayload = match ($automation->action_type) {
                AutomationActionType::WEBHOOK => $this->executeWebhookAction($automation, $event, $context),
                AutomationActionType::MESSAGE => $this->executeMessageAction($automation, $context),
            };

            $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);

            $log = $this->createLog($automation, $event, $context, [
                'status' => AutomationLogStatus::EXECUTED,
                'condition_expression' => $automation->condition,
                'condition_result' => true,
                'action_payload' => $actionPayload,
                'execution_time_ms' => $executionTimeMs,
            ]);

            return new AutomationResultData(
                status: AutomationLogStatus::EXECUTED,
                log: $log,
                executionTimeMs: $executionTimeMs,
            );
        } catch (Throwable $throwable) {
            $executionTimeMs = (int) ((microtime(true) - $startTime) * 1000);

            Log::error('Automation execution failed', [
                'automation_id' => $automation->id,
                'trigger' => $event->getTriggerType(),
                'error' => $throwable->getMessage(),
            ]);

            $log = $this->createLog($automation, $event, $context, [
                'status' => AutomationLogStatus::FAILED,
                'condition_expression' => $automation->condition,
                'error_message' => $throwable->getMessage(),
                'execution_time_ms' => $executionTimeMs,
            ]);

            return new AutomationResultData(
                status: AutomationLogStatus::FAILED,
                log: $log,
                errorMessage: $throwable->getMessage(),
                executionTimeMs: $executionTimeMs,
            );
        }
    }

    /**
     * Test an automation condition without executing the action.
     *
     * @param  array<string, mixed>  $context
     * @return array{valid: bool, result: mixed, error: string|null}
     */
    public function testCondition(string $condition, array $context): array
    {
        try {
            $result = $this->expressionLanguageService->evaluate($condition, $context);

            return [
                'valid' => true,
                'result' => $result,
                'error' => null,
            ];
        } catch (ExpressionEvaluationException $expressionEvaluationException) {
            return [
                'valid' => false,
                'result' => null,
                'error' => $expressionEvaluationException->getMessage(),
            ];
        }
    }

    /**
     * Preview a webhook payload template with sample context.
     *
     * @param  array<string, mixed>  $context
     * @return array{valid: bool, result: array<string, mixed>|null, error: string|null}
     */
    public function previewWebhookPayload(string $template, array $context): array
    {
        try {
            $decoded = json_decode($template, true);
            if (! is_array($decoded)) {
                return [
                    'valid' => false,
                    'result' => null,
                    'error' => 'Invalid JSON format',
                ];
            }

            $result = $this->evaluatePayloadTemplate($decoded, $context);

            return [
                'valid' => true,
                'result' => $result,
                'error' => null,
            ];
        } catch (Throwable $throwable) {
            return [
                'valid' => false,
                'result' => null,
                'error' => $throwable->getMessage(),
            ];
        }
    }

    /**
     * Preview a message template with sample context.
     *
     * @param  array<string, mixed>  $context
     * @return array{valid: bool, result: string|null, error: string|null}
     */
    public function previewMessageTemplate(string $template, array $context): array
    {
        try {
            $result = $this->evaluateMessageTemplate($template, $context);

            return [
                'valid' => true,
                'result' => $result,
                'error' => null,
            ];
        } catch (Throwable $throwable) {
            return [
                'valid' => false,
                'result' => null,
                'error' => $throwable->getMessage(),
            ];
        }
    }

    /**
     * Execute webhook action.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function executeWebhookAction(Automation $automation, AutomationTriggerable $event, array $context): array
    {
        $webhook = $automation->webhook;

        if (! $webhook) {
            throw new RuntimeException('Automation webhook not found');
        }

        // If a custom payload template is defined, use it; otherwise use the model
        $payload = $this->buildWebhookPayload($automation, $context, $event);

        WebhookService::dispatch($webhook, $event->getTriggerType(), $payload);

        return [
            'type' => 'webhook',
            'webhook_id' => $webhook->id,
            'webhook_url' => $webhook->url,
            'custom_payload' => filled($automation->webhook_payload_template),
        ];
    }

    /**
     * Build the webhook payload from template or use default model.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>|Model
     */
    protected function buildWebhookPayload(Automation $automation, array $context, AutomationTriggerable $event): array|Model
    {
        $template = $automation->webhook_payload_template;

        // If no custom template, return the subject model directly
        if (blank($template)) {
            return $event->getSubject();
        }

        // Handle JSON string (from form) or array (from cast)
        if (is_string($template)) {
            $template = json_decode($template, true);
            if (! is_array($template)) {
                return $event->getSubject();
            }
        }

        // Recursively evaluate template values
        return $this->evaluatePayloadTemplate($template, $context);
    }

    /**
     * Recursively evaluate payload template, replacing {{ placeholders }} with context values.
     *
     * @param  array<string, mixed>  $template
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function evaluatePayloadTemplate(array $template, array $context): array
    {
        $result = [];

        foreach ($template as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->evaluatePayloadTemplate($value, $context);
            } elseif (is_string($value)) {
                $result[$key] = $this->evaluateTemplateValue($value, $context);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Evaluate a single template value using Twig.
     *
     * @param  array<string, mixed>  $context
     */
    protected function evaluateTemplateValue(string $value, array $context): mixed
    {
        // If no Twig syntax, return as-is
        if (! str_contains($value, '{{')) {
            return $value;
        }

        // Convert stdClass objects to arrays for Twig compatibility
        $twigContext = $this->prepareContextForTwig($context);

        return $this->twig->createTemplate($value)->render($twigContext);
    }

    /**
     * Prepare context for Twig by converting stdClass objects to arrays.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function prepareContextForTwig(array $context): array
    {
        return array_map(function ($value) {
            if ($value instanceof stdClass) {
                return (array) $value;
            }

            if (is_array($value)) {
                return $this->prepareContextForTwig($value);
            }

            return $value;
        }, $context);
    }

    /**
     * Execute message action - creates Message model using channels from referenced message.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function executeMessageAction(Automation $automation, array $context): array
    {
        $sourceMessage = $automation->message;

        if (! $sourceMessage) {
            throw new RuntimeException('Automation message not found');
        }

        // Use automation's template override, or fall back to source message content
        $template = filled($automation->message_template)
            ? $automation->message_template
            : $sourceMessage->message;
        $messageContent = $this->evaluateMessageTemplate($template ?? '', $context);
        $recipients = $this->evaluateRecipients($automation->message_recipients_expression ?? '', $context);

        // Use channels from the referenced message
        $channels = $sourceMessage->channels;

        $message = Message::create([
            'message' => $messageContent,
            'channels' => $channels,
            'recipients' => $recipients === [] ? null : collect($recipients),
            'repeats' => false,
            'send_at' => Carbon::now(),
        ]);

        return [
            'type' => 'message',
            'source_message_id' => $sourceMessage->id,
            'created_message_id' => $message->id,
            'message_content' => $messageContent,
            'recipients' => $recipients,
            'channels' => $channels?->toArray(),
        ];
    }

    /**
     * Evaluate message template using Twig.
     *
     * @param  array<string, mixed>  $context
     */
    protected function evaluateMessageTemplate(string $template, array $context): string
    {
        // If no Twig syntax, return as-is
        if (! str_contains($template, '{{')) {
            return $template;
        }

        // Convert stdClass objects to arrays for Twig compatibility
        $twigContext = $this->prepareContextForTwig($context);

        return $this->twig->createTemplate($template)->render($twigContext);
    }

    /**
     * Evaluate recipients expression to get array of user IDs.
     *
     * @param  array<string, mixed>  $context
     * @return array<int>
     */
    protected function evaluateRecipients(string $expression, array $context): array
    {
        if (blank($expression)) {
            return [];
        }

        try {
            $result = $this->expressionLanguageService->evaluate($expression, $context);

            // Normalize result to array
            if (is_numeric($result)) {
                return [(int) $result];
            }

            if (is_array($result)) {
                return array_map(intval(...), array_filter($result, is_numeric(...)));
            }

            return [];
        } catch (ExpressionEvaluationException) {
            return [];
        }
    }

    /**
     * Create an automation log entry.
     *
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $attributes
     */
    protected function createLog(
        Automation $automation,
        AutomationTriggerable $event,
        array $context,
        array $attributes
    ): AutomationLog {
        return AutomationLog::create([
            'automation_id' => $automation->id,
            'trigger' => $event->getTriggerType(),
            'subject_type' => $event->getSubject()::class,
            'subject_id' => $event->getSubject()->getKey(),
            'causer_type' => $event->getCauser() instanceof Model ? $event->getCauser()::class : null,
            'causer_id' => $event->getCauser()?->getKey(),
            'context' => $context,
            ...$attributes,
        ]);
    }
}
