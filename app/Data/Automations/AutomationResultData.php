<?php

declare(strict_types=1);

namespace App\Data\Automations;

use App\Models\AutomationLog;
use App\Models\Enums\AutomationLogStatus;
use Spatie\LaravelData\Data;

class AutomationResultData extends Data
{
    public function __construct(
        public AutomationLogStatus $status,
        public ?AutomationLog $log = null,
        public ?string $errorMessage = null,
        public ?int $executionTimeMs = null,
    ) {}
}
