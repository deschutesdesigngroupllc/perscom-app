<?php

declare(strict_types=1);

namespace App\Data\Automations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class AutomationContextData extends Data
{
    /**
     * @param  array<string, mixed>  $model
     * @param  array<string, array{old: mixed, new: mixed}>|null  $changes
     * @param  array<string, mixed>|null  $causer
     */
    public function __construct(
        public array $model,
        public string $modelType,
        public int|string $modelId,
        public ?array $changes,
        public ?array $causer,
        public int|string|null $causerId,
        public Carbon $now,
    ) {}

    /**
     * Create context from a model and optional causer.
     *
     * @param  array<string, array{old: mixed, new: mixed}>|null  $changedAttributes
     */
    public static function fromModel(
        Model $subject,
        ?Model $causer = null,
        ?array $changedAttributes = null,
    ): self {
        return new self(
            model: $subject->toArray(),
            modelType: $subject::class,
            modelId: $subject->getKey(),
            changes: $changedAttributes,
            causer: $causer?->toArray(),
            causerId: $causer?->getKey(),
            now: Carbon::now(),
        );
    }

    /**
     * Convert to array for expression evaluation and Twig templates.
     *
     * @return array<string, mixed>
     */
    public function toExpressionArray(): array
    {
        return [
            'model' => $this->model,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'changes' => $this->changes,
            'causer' => $this->causer,
            'causer_id' => $this->causerId,
            'now' => $this->now,
        ];
    }
}
