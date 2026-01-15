<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\AutomationLogStatus;
use App\Models\Enums\AutomationTrigger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $automation_id
 * @property string $trigger
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property AutomationLogStatus $status
 * @property string|null $condition_expression
 * @property bool|null $condition_result
 * @property array<string, mixed>|null $context
 * @property array<string, mixed>|null $action_payload
 * @property string|null $error_message
 * @property int|null $execution_time_ms
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Automation $automation
 * @property-read Model|null $subject
 * @property-read Model|null $causer
 *
 * @method static \Database\Factories\AutomationLogFactory factory($count = null, $state = [])
 * @method static Builder<static>|AutomationLog newModelQuery()
 * @method static Builder<static>|AutomationLog newQuery()
 * @method static Builder<static>|AutomationLog query()
 *
 * @mixin \Eloquent
 */
class AutomationLog extends Model
{
    use HasFactory;

    protected $table = 'automations_logs';

    protected $fillable = [
        'automation_id',
        'trigger',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'status',
        'condition_expression',
        'condition_result',
        'context',
        'action_payload',
        'error_message',
        'execution_time_ms',
    ];

    /**
     * @return BelongsTo<Automation, $this>
     */
    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'trigger' => AutomationTrigger::class,
            'status' => AutomationLogStatus::class,
            'condition_result' => 'boolean',
            'context' => 'array',
            'action_payload' => 'array',
            'execution_time_ms' => 'integer',
        ];
    }
}
