<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationTrigger;
use App\Models\Enums\ModelUpdateLookupType;
use App\Models\Enums\NotificationChannel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property AutomationTrigger $trigger
 * @property string|null $condition
 * @property AutomationActionType $action_type
 * @property int|null $webhook_id
 * @property array<array-key, mixed>|null $webhook_payload_template
 * @property \Illuminate\Support\Collection<int, NotificationChannel>|null $message_channels
 * @property string|null $message_content
 * @property string|null $message_recipients_expression
 * @property string|null $model_update_target
 * @property ModelUpdateLookupType|null $model_update_lookup_type
 * @property string|null $model_update_lookup_expression
 * @property array<array-key, mixed>|null $model_update_lookup_conditions
 * @property array<array-key, mixed>|null $model_update_fields
 * @property bool $enabled
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, AutomationLog> $logs
 * @property-read int|null $logs_count
 * @property-read Webhook|null $webhook
 *
 * @method static Builder<static>|Automation enabled()
 * @method static \Database\Factories\AutomationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Automation forTrigger(\App\Models\Enums\AutomationTrigger|string $trigger)
 * @method static Builder<static>|Automation messageActions()
 * @method static Builder<static>|Automation modelUpdateActions()
 * @method static Builder<static>|Automation newModelQuery()
 * @method static Builder<static>|Automation newQuery()
 * @method static Builder<static>|Automation query()
 * @method static Builder<static>|Automation webhookActions()
 * @method static Builder<static>|Automation whereActionType($value)
 * @method static Builder<static>|Automation whereCondition($value)
 * @method static Builder<static>|Automation whereCreatedAt($value)
 * @method static Builder<static>|Automation whereDescription($value)
 * @method static Builder<static>|Automation whereEnabled($value)
 * @method static Builder<static>|Automation whereId($value)
 * @method static Builder<static>|Automation whereMessageChannels($value)
 * @method static Builder<static>|Automation whereMessageContent($value)
 * @method static Builder<static>|Automation whereMessageRecipientsExpression($value)
 * @method static Builder<static>|Automation whereModelUpdateFields($value)
 * @method static Builder<static>|Automation whereModelUpdateLookupConditions($value)
 * @method static Builder<static>|Automation whereModelUpdateLookupExpression($value)
 * @method static Builder<static>|Automation whereModelUpdateLookupType($value)
 * @method static Builder<static>|Automation whereModelUpdateTarget($value)
 * @method static Builder<static>|Automation whereName($value)
 * @method static Builder<static>|Automation wherePriority($value)
 * @method static Builder<static>|Automation whereTrigger($value)
 * @method static Builder<static>|Automation whereUpdatedAt($value)
 * @method static Builder<static>|Automation whereWebhookId($value)
 * @method static Builder<static>|Automation whereWebhookPayloadTemplate($value)
 *
 * @mixin \Eloquent
 */
class Automation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger',
        'condition',
        'action_type',
        'webhook_id',
        'webhook_payload_template',
        'message_channels',
        'message_content',
        'message_recipients_expression',
        'model_update_target',
        'model_update_lookup_type',
        'model_update_lookup_expression',
        'model_update_lookup_conditions',
        'model_update_fields',
        'enabled',
        'priority',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    public function scopeForTrigger(Builder $query, AutomationTrigger|string $trigger): Builder
    {
        $value = $trigger instanceof AutomationTrigger ? $trigger->value : $trigger;

        return $query->where('trigger', $value);
    }

    public function scopeWebhookActions(Builder $query): Builder
    {
        return $query->where('action_type', AutomationActionType::WEBHOOK);
    }

    public function scopeMessageActions(Builder $query): Builder
    {
        return $query->where('action_type', AutomationActionType::MESSAGE);
    }

    public function isWebhookAction(): bool
    {
        return $this->action_type === AutomationActionType::WEBHOOK;
    }

    public function isMessageAction(): bool
    {
        return $this->action_type === AutomationActionType::MESSAGE;
    }

    public function scopeModelUpdateActions(Builder $query): Builder
    {
        return $query->where('action_type', AutomationActionType::MODEL_UPDATE);
    }

    public function isModelUpdateAction(): bool
    {
        return $this->action_type === AutomationActionType::MODEL_UPDATE;
    }

    protected function casts(): array
    {
        return [
            'trigger' => AutomationTrigger::class,
            'action_type' => AutomationActionType::class,
            'webhook_payload_template' => 'array',
            'message_channels' => AsEnumCollection::of(NotificationChannel::class),
            'model_update_lookup_type' => ModelUpdateLookupType::class,
            'model_update_lookup_conditions' => 'array',
            'model_update_fields' => 'array',
            'enabled' => 'boolean',
            'priority' => 'integer',
        ];
    }
}
