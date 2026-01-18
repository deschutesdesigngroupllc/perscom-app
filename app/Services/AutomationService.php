<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AutomationTriggerable;
use App\Data\Automations\AutomationContextData;
use App\Data\Automations\AutomationResultData;
use App\Exceptions\ExpressionEvaluationException;
use App\Models\Automation;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\ModelUpdateLookupType;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use stdClass;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

class AutomationService
{
    public function __construct(
        protected ExpressionLanguageService $expressionLanguageService,
        protected Environment $twig,
    ) {}

    /**
     * Get all updatable models configuration.
     *
     * @return array<string, array{model: class-string<Model>, label: string, fields: array<string, array{type: string, label: string}>}>
     */
    public static function getUpdatableModels(): array
    {
        /** @var array<string, array{model: class-string<Model>, label: string, fields: array<string, array{type: string, label: string}>}> $models */
        $models = config('automations.updatable_models', []);

        return $models;
    }

    /**
     * Get all automation templates.
     *
     * @return array<string, array{name: string, description: string, category: string, data: array<string, mixed>}>
     */
    public static function getTemplates(): array
    {
        /** @var array<string, array{name: string, description: string, category: string, data: array<string, mixed>}> $templates */
        $templates = config('automations.templates', []);

        return $templates;
    }

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
        $contextArray = $context->toExpressionArray();

        try {
            $conditionResult = true;
            if (filled($automation->condition)) {
                $conditionResult = (bool) $this->expressionLanguageService->evaluate(
                    $automation->condition,
                    $contextArray
                );
            }

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

            $actionPayload = match ($automation->action_type) {
                AutomationActionType::WEBHOOK => $this->executeWebhookAction($automation, $event, $context),
                AutomationActionType::MESSAGE => $this->executeMessageAction($automation, $context),
                AutomationActionType::MODEL_UPDATE => $this->executeModelUpdateAction($automation, $context),
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
     * Preview a message template with a sample context.
     *
     * @param  array<string, mixed>  $context
     * @return array{valid: bool, result: string|null, error: string|null}
     */
    public function previewMessageTemplate(string $template, array $context): array
    {
        try {
            $twigContext = $this->prepareContextForTwig($context);
            $result = $this->twig->createTemplate($template)->render($twigContext);

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
     * @return array<string, mixed>
     */
    protected function executeWebhookAction(Automation $automation, AutomationTriggerable $event, AutomationContextData $context): array
    {
        $webhook = $automation->webhook;

        if (! $webhook) {
            throw new RuntimeException('Automation webhook not found');
        }

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
     * @return array<string, mixed>|Model
     */
    protected function buildWebhookPayload(Automation $automation, AutomationContextData $context, AutomationTriggerable $event): array|Model
    {
        $template = $automation->webhook_payload_template;

        if (blank($template)) {
            return $event->getSubject();
        }

        if (is_string($template)) {
            $template = json_decode($template, true);
            if (! is_array($template)) {
                return $event->getSubject();
            }
        }

        return $this->evaluatePayloadTemplate($template, $context->toExpressionArray());
    }

    /**
     * Recursively evaluate the payload template, replacing {{ placeholders }} with context values.
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
        if (! str_contains($value, '{{')) {
            return $value;
        }

        $twigContext = $this->prepareContextForTwig($context);

        try {
            return $this->twig->createTemplate($value)->render($twigContext);
        } catch (LoaderError|SyntaxError) {
            return $value;
        }
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
     * Execute message action - creates a Message model using automation's configured channels and content.
     *
     * @return array<string, mixed>
     */
    protected function executeMessageAction(Automation $automation, AutomationContextData $context): array
    {
        if (blank($automation->message_content) || blank($automation->message_channels)) {
            throw new RuntimeException('Automation message content or channels not configured');
        }

        $contextArray = $context->toExpressionArray();
        $messageContent = $this->evaluateMessageTemplate($automation->message_content, $contextArray);
        $recipients = $this->evaluateRecipients($automation->message_recipients_expression ?? '', $contextArray);
        $channels = $automation->message_channels;

        $message = Message::create([
            'message' => $messageContent,
            'channels' => $channels,
            'recipients' => $recipients === [] ? null : collect($recipients),
            'repeats' => false,
            'send_at' => Carbon::now(),
        ]);

        return [
            'type' => 'message',
            'created_message_id' => $message->id,
            'message_content' => $messageContent,
            'recipients' => $recipients,
            'channels' => $channels?->toArray(),
        ];
    }

    /**
     * Execute model update action - updates a model in the database.
     *
     * @return array<string, mixed>
     *
     * @throws ExpressionEvaluationException
     */
    protected function executeModelUpdateAction(Automation $automation, AutomationContextData $context): array
    {
        $targetKey = $automation->model_update_target;
        $lookupType = $automation->model_update_lookup_type;
        $fieldMappings = $automation->model_update_fields;

        if (blank($targetKey) || blank($lookupType) || blank($fieldMappings)) {
            throw new RuntimeException('Model update action not properly configured');
        }

        $modelConfig = $this->getUpdatableModelConfig($targetKey);
        if ($modelConfig === null) {
            throw new RuntimeException(sprintf("Model '%s' is not configured for updates", $targetKey));
        }

        $modelClass = $modelConfig['model'];
        $contextArray = $context->toExpressionArray();

        $targetModel = $this->findTargetModel($automation, $modelClass, $contextArray);
        if (! $targetModel instanceof Model) {
            throw new RuntimeException('Target model not found');
        }

        $contextWithTarget = array_merge($contextArray, [
            'target' => $targetModel->toArray(),
        ]);

        $evaluatedFields = $this->evaluateFieldMappings($fieldMappings, $contextWithTarget);
        $filteredFields = $this->filterAllowedFields($evaluatedFields, $modelConfig);

        if ($filteredFields === []) {
            throw new RuntimeException('No valid fields to update after filtering');
        }

        $originalValues = [];
        foreach (array_keys($filteredFields) as $field) {
            $originalValues[$field] = $targetModel->{$field};
        }

        $targetModel->fill($filteredFields);
        $targetModel->save();

        return [
            'type' => 'model_update',
            'target_model' => $targetKey,
            'target_id' => $targetModel->getKey(),
            'updated_fields' => array_keys($filteredFields),
            'original_values' => $originalValues,
            'new_values' => $filteredFields,
        ];
    }

    /**
     * Get the configuration for an updatable model.
     *
     * @return array{model: class-string<Model>, label: string, allowed_fields: list<string>, denied_fields: list<string>}|null
     */
    protected function getUpdatableModelConfig(string $key): ?array
    {
        /** @var array<string, array{model: class-string<Model>, label: string, allowed_fields: list<string>, denied_fields: list<string>}> $models */
        $models = config('automations.updatable_models', []);

        return $models[$key] ?? null;
    }

    /**
     * Filter fields based on allowed and denied lists.
     *
     * @param  array<string, mixed>  $fields
     * @param  array{model: class-string<Model>, label: string, allowed_fields: list<string>, denied_fields: list<string>}  $modelConfig
     * @return array<string, mixed>
     */
    protected function filterAllowedFields(array $fields, array $modelConfig): array
    {
        $allowedFields = $modelConfig['allowed_fields'] ?? [];
        $deniedFields = $modelConfig['denied_fields'] ?? [];

        $filtered = in_array('*', $allowedFields, true) ? $fields : array_intersect_key($fields, array_flip($allowedFields));

        foreach ($deniedFields as $deniedField) {
            unset($filtered[$deniedField]);
        }

        return $filtered;
    }

    /**
     * Find the target model based on lookup configuration.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $context
     *
     * @throws ExpressionEvaluationException
     */
    protected function findTargetModel(Automation $automation, string $modelClass, array $context): ?Model
    {
        if ($automation->model_update_lookup_type === ModelUpdateLookupType::EXPRESSION) {
            $expression = $automation->model_update_lookup_expression;
            if (blank($expression)) {
                return null;
            }

            $id = $this->expressionLanguageService->evaluate($expression, $context);
            if (! is_numeric($id)) {
                return null;
            }

            return $modelClass::query()->find((int) $id);
        }

        if ($automation->model_update_lookup_type === ModelUpdateLookupType::QUERY) {
            $conditions = $automation->model_update_lookup_conditions;
            if (blank($conditions) || ! is_array($conditions)) {
                return null;
            }

            $query = $modelClass::query();
            foreach ($conditions as $field => $valueTemplate) {
                $value = $this->evaluateTemplateValue((string) $valueTemplate, $context);
                $query->where($field, $value);
            }

            return $query->first();
        }

        return null;
    }

    /**
     * Evaluate field mappings using Twig templates.
     *
     * @param  array<string, mixed>  $fieldMappings
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    protected function evaluateFieldMappings(array $fieldMappings, array $context): array
    {
        $result = [];

        foreach ($fieldMappings as $field => $valueTemplate) {
            $evaluated = $this->evaluateTemplateValue((string) $valueTemplate, $context);

            if (is_numeric($evaluated) && ! str_contains((string) $valueTemplate, '{{')) {
                $result[$field] = is_float($evaluated + 0) ? (float) $evaluated : (int) $evaluated;
            } elseif ($evaluated === 'true' || $evaluated === 'false') {
                $result[$field] = $evaluated === 'true';
            } elseif (is_numeric($evaluated)) {
                $result[$field] = is_float($evaluated + 0) ? (float) $evaluated : (int) $evaluated;
            } else {
                $result[$field] = $evaluated;
            }
        }

        return $result;
    }

    /**
     * Evaluate message template using Twig.
     *
     * @param  array<string, mixed>  $context
     */
    protected function evaluateMessageTemplate(string $template, array $context): string
    {
        if (! str_contains($template, '{{')) {
            return $template;
        }

        $twigContext = $this->prepareContextForTwig($context);

        try {
            return $this->twig->createTemplate($template)->render($twigContext);
        } catch (LoaderError|SyntaxError) {
            return $template;
        }
    }

    /**
     * Evaluate recipients expression to get an array of user IDs.
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
     * @param  array<string, mixed>  $attributes
     */
    protected function createLog(
        Automation $automation,
        AutomationTriggerable $event,
        AutomationContextData $context,
        array $attributes
    ): AutomationLog {
        return AutomationLog::create([
            'automation_id' => $automation->id,
            'trigger' => $event->getTriggerType(),
            'subject_type' => $event->getSubject()::class,
            'subject_id' => $event->getSubject()->getKey(),
            'causer_type' => $event->getCauser() instanceof Model ? $event->getCauser()::class : null,
            'causer_id' => $event->getCauser()?->getKey(),
            'context' => $context->toExpressionArray(),
            ...$attributes,
        ]);
    }
}
