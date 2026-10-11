<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Data\Automations\AutomationContextData;
use App\Models\Enums\AutomationTrigger;
use Illuminate\Database\Eloquent\Model;

class ScheduledTriggerFired extends AbstractAutomationEvent
{
    /**
     * @param  array<string, mixed>  $additionalContext
     */
    public function __construct(
        Model $subject,
        protected AutomationTrigger $trigger,
        protected array $additionalContext = [],
    ) {
        parent::__construct($subject);
    }

    public function getTriggerType(): string
    {
        return $this->trigger->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAdditionalContext(): array
    {
        return $this->additionalContext;
    }

    public function getExpressionContext(): AutomationContextData
    {
        $context = parent::getExpressionContext();
        $context->model = [...$context->model, ...$this->additionalContext];

        return $context;
    }
}
