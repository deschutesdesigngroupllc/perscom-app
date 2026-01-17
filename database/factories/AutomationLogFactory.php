<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Automation;
use App\Models\AutomationLog;
use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\AutomationTrigger;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AutomationLog>
 */
class AutomationLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'automation_id' => Automation::factory(),
            'trigger' => $this->faker->randomElement(AutomationTrigger::cases())->value,
            'subject_type' => User::class,
            'subject_id' => null,
            'causer_type' => null,
            'causer_id' => null,
            'status' => AutomationLogStatus::EXECUTED,
            'condition_expression' => null,
            'condition_result' => true,
            'context' => [
                'model' => [
                    'id' => $this->faker->randomNumber(),
                    'name' => $this->faker->name,
                ],
            ],
            'action_payload' => [
                'type' => 'webhook',
                'url' => $this->faker->url,
            ],
            'error_message' => null,
            'execution_time_ms' => $this->faker->numberBetween(10, 500),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AutomationLogStatus::PENDING,
            'condition_result' => null,
            'action_payload' => null,
            'execution_time_ms' => null,
        ]);
    }

    public function conditionFailed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AutomationLogStatus::CONDITION_FAILED,
            'condition_expression' => 'model.status == "active"',
            'condition_result' => false,
            'action_payload' => null,
        ]);
    }

    public function failed(string $errorMessage = 'An error occurred'): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AutomationLogStatus::FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    public function executed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => AutomationLogStatus::EXECUTED,
            'condition_result' => true,
            'error_message' => null,
        ]);
    }

    public function forAutomation(Automation $automation): static
    {
        return $this->state(fn (array $attributes): array => [
            'automation_id' => $automation->id,
            'trigger' => $automation->trigger->value,
        ]);
    }
}
