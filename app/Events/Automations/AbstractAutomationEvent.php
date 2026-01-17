<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Contracts\AutomationTriggerable;
use App\Data\Automations\AutomationContextData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

abstract class AbstractAutomationEvent implements AutomationTriggerable
{
    use Dispatchable;
    use SerializesModels;

    protected ?Model $causer = null;

    /**
     * @param  array<string, array{old: mixed, new: mixed}>|null  $changedAttributes
     */
    public function __construct(
        protected Model $subject,
        protected ?array $changedAttributes = null,
    ) {
        $this->causer = Auth::user();
    }

    abstract public function getTriggerType(): string;

    /**
     * Build changed attributes array from model's dirty attributes.
     *
     * @return array<string, array{old: mixed, new: mixed}>|null
     */
    public static function buildChangedAttributes(Model $model): ?array
    {
        $changes = $model->getChanges();

        if (empty($changes)) {
            return null;
        }

        $result = [];
        foreach ($changes as $key => $newValue) {
            $result[$key] = [
                'old' => $model->getOriginal($key),
                'new' => $newValue,
            ];
        }

        return $result;
    }

    public function getSubject(): Model
    {
        return $this->subject;
    }

    public function getChangedAttributes(): ?array
    {
        return $this->changedAttributes;
    }

    public function getCauser(): ?Model
    {
        return $this->causer;
    }

    public function getExpressionContext(): AutomationContextData
    {
        return AutomationContextData::fromModel(
            subject: $this->subject,
            causer: $this->causer,
            changedAttributes: $this->changedAttributes,
        );
    }
}
