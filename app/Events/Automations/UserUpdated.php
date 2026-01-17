<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Models\Enums\AutomationTrigger;

class UserUpdated extends AbstractAutomationEvent
{
    public function getTriggerType(): string
    {
        return AutomationTrigger::USER_UPDATED->value;
    }
}
