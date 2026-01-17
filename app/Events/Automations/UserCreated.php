<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Models\Enums\AutomationTrigger;

class UserCreated extends AbstractAutomationEvent
{
    public function getTriggerType(): string
    {
        return AutomationTrigger::USER_CREATED->value;
    }
}
