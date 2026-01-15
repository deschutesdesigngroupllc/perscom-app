<?php

declare(strict_types=1);

namespace App\Data\Automations;

use App\Contracts\AutomationTriggerable;
use App\Models\Automation;
use Spatie\LaravelData\Data;

class AutomationExecutionData extends Data
{
    public function __construct(
        public Automation $automation,
        public AutomationTriggerable $event,
        public AutomationContextData $context,
    ) {}
}
