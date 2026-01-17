<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\AutomationActionType;
use App\Models\Enums\AutomationTrigger;
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
 * @property array<string, mixed>|null $webhook_payload_template
 * @property \Illuminate\Support\Collection<int, NotificationChannel>|null $message_channels
 * @property string|null $message_content
 * @property string|null $message_recipients_expression
 * @property bool $enabled
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Webhook|null $webhook
 * @property-read Collection<int, AutomationLog> $logs
 * @property-read int|null $logs_count
 *
 * @method static \Database\Factories\AutomationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Automation newModelQuery()
 * @method static Builder<static>|Automation newQuery()
 * @method static Builder<static>|Automation query()
 * @method static Builder<static>|Automation enabled()
 * @method static Builder<static>|Automation forTrigger(AutomationTrigger|string $trigger)
 * @method static Builder<static>|Automation webhookActions()
 * @method static Builder<static>|Automation messageActions()
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
        'enabled',
        'priority',
    ];

    /**
     * @return BelongsTo<Webhook, $this>
     */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    /**
     * @return HasMany<AutomationLog, $this>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AutomationLog::class);
    }

    /**
     * Scope to only enabled automations.
     *
     * @param  Builder<Automation>  $query
     * @return Builder<Automation>
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to automations for a specific trigger.
     *
     * @param  Builder<Automation>  $query
     * @return Builder<Automation>
     */
    public function scopeForTrigger(Builder $query, AutomationTrigger|string $trigger): Builder
    {
        $value = $trigger instanceof AutomationTrigger ? $trigger->value : $trigger;

        return $query->where('trigger', $value);
    }

    /**
     * Scope to only webhook actions.
     *
     * @param  Builder<Automation>  $query
     * @return Builder<Automation>
     */
    public function scopeWebhookActions(Builder $query): Builder
    {
        return $query->where('action_type', AutomationActionType::WEBHOOK);
    }

    /**
     * Scope to only message actions.
     *
     * @param  Builder<Automation>  $query
     * @return Builder<Automation>
     */
    public function scopeMessageActions(Builder $query): Builder
    {
        return $query->where('action_type', AutomationActionType::MESSAGE);
    }

    /**
     * Check if this automation sends a webhook.
     */
    public function isWebhookAction(): bool
    {
        return $this->action_type === AutomationActionType::WEBHOOK;
    }

    /**
     * Check if this automation sends a message.
     */
    public function isMessageAction(): bool
    {
        return $this->action_type === AutomationActionType::MESSAGE;
    }

    protected function casts(): array
    {
        return [
            'trigger' => AutomationTrigger::class,
            'action_type' => AutomationActionType::class,
            'webhook_payload_template' => 'array',
            'message_channels' => AsEnumCollection::of(NotificationChannel::class),
            'enabled' => 'boolean',
            'priority' => 'integer',
        ];
    }
}
