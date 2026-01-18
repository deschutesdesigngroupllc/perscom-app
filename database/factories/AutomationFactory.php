<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Automation;
use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\ModelUpdateLookupType;
use App\Models\Enums\NotificationChannel;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Automation>
 */
class AutomationFactory extends Factory
{
    public function definition(): array
    {
        $actionType = $this->faker->randomElement(AutomationActionType::cases());

        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence,
            'trigger' => $this->faker->randomElement(AutomationTrigger::cases()),
            'condition' => null,
            'action_type' => $actionType,
            'webhook_id' => $actionType === AutomationActionType::WEBHOOK ? Webhook::factory() : null,
            'webhook_payload_template' => null,
            'message_channels' => $actionType === AutomationActionType::MESSAGE ? [NotificationChannel::DATABASE] : null,
            'message_content' => $actionType === AutomationActionType::MESSAGE ? $this->faker->sentence : null,
            'message_recipients_expression' => null,
            'enabled' => true,
            'priority' => 0,
        ];
    }

    public function webhookAction(): static
    {
        return $this->state(fn (array $attributes): array => [
            'action_type' => AutomationActionType::WEBHOOK,
            'webhook_id' => Webhook::factory(),
            'webhook_payload_template' => null,
            'message_channels' => null,
            'message_content' => null,
            'message_recipients_expression' => null,
        ]);
    }

    public function messageAction(): static
    {
        return $this->state(fn (array $attributes): array => [
            'action_type' => AutomationActionType::MESSAGE,
            'webhook_id' => null,
            'webhook_payload_template' => null,
            'message_channels' => [NotificationChannel::DATABASE],
            'message_content' => $this->faker->sentence,
            'message_recipients_expression' => null,
        ]);
    }

    public function modelUpdateAction(): static
    {
        return $this->state(fn (array $attributes): array => [
            'action_type' => AutomationActionType::MODEL_UPDATE,
            'webhook_id' => null,
            'webhook_payload_template' => null,
            'message_channels' => null,
            'message_content' => null,
            'message_recipients_expression' => null,
            'model_update_target' => 'user',
            'model_update_lookup_type' => ModelUpdateLookupType::EXPRESSION,
            'model_update_lookup_expression' => 'model["id"]',
            'model_update_lookup_conditions' => null,
            'model_update_fields' => ['notes' => 'Updated via automation'],
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'enabled' => false,
        ]);
    }

    public function withCondition(string $condition = 'model.status == "active"'): static
    {
        return $this->state(fn (array $attributes): array => [
            'condition' => $condition,
        ]);
    }

    public function forTrigger(AutomationTrigger $trigger): static
    {
        return $this->state(fn (array $attributes): array => [
            'trigger' => $trigger,
        ]);
    }
}
