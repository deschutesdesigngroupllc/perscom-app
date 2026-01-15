<?php

declare(strict_types=1);

namespace App\Events\Automations;

use App\Contracts\AutomationTriggerable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

abstract class AbstractAutomationEvent implements AutomationTriggerable
{
    use Dispatchable;
    use SerializesModels;

    protected ?Model $causer = null;

    public function __construct(protected Model $subject, /**
     * @var array<string, array{old: mixed, new: mixed}>|null
     */
        protected ?array $changedAttributes = null)
    {
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

    public function getExpressionContext(): array
    {
        $modelArray = $this->subject->toArray();

        return [
            'model' => $modelArray,
            'model_type' => $this->subject::class,
            'model_id' => $this->subject->getKey(),
            'changes' => $this->changedAttributes,
            'causer' => $this->causer?->toArray(),
            'causer_id' => $this->causer?->getKey(),
            'now' => Carbon::now(),
        ];
    }
}
