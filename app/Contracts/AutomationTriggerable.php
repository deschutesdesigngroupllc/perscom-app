<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Data\Automations\AutomationContextData;
use Illuminate\Database\Eloquent\Model;

interface AutomationTriggerable
{
    /**
     * Get the trigger type identifier (e.g., 'user.created', 'awardrecord.updated').
     */
    public function getTriggerType(): string;

    /**
     * Get the subject model that triggered the event.
     */
    public function getSubject(): Model;

    /**
     * Get the attributes that were changed (for update events).
     * Returns null for create/delete events.
     *
     * @return array<string, array{old: mixed, new: mixed}>|null
     */
    public function getChangedAttributes(): ?array;

    /**
     * Get context data available for expression evaluation.
     */
    public function getExpressionContext(): AutomationContextData;

    /**
     * Get the user who caused this event (if applicable).
     */
    public function getCauser(): ?Model;
}
