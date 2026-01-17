<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Models\Enums\AutomationTrigger;
use Illuminate\Database\Eloquent\Model;

class RecordDeleted extends AbstractAutomationEvent
{
    public function __construct(Model $subject, protected AutomationTrigger $trigger)
    {
        parent::__construct($subject);
    }

    public function getTriggerType(): string
    {
        return $this->trigger->value;
    }
}
