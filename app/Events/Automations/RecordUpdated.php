<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Models\Enums\AutomationTrigger;
use Illuminate\Database\Eloquent\Model;

class RecordUpdated extends AbstractAutomationEvent
{
    public function __construct(Model $subject, protected AutomationTrigger $trigger, ?array $changedAttributes = null)
    {
        parent::__construct($subject, $changedAttributes);
    }

    public function getTriggerType(): string
    {
        return $this->trigger->value;
    }
}
