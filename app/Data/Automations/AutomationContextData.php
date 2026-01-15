<?php

declare(strict_types=1);

namespace App\Data\Automations;

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
}
