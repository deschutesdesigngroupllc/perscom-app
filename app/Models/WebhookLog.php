<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\WebhookEvent;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Optional;

/**
 * @property int $id
 * @property string|null $log_name
 * @property array $description
 * @property string|null $subject_type
 * @property-read Optional|WebhookEvent|null|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property \Illuminate\Support\Collection|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $causer
 * @property-read mixed|null $data
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $subject
 *
 * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder|Activity forBatch(string $batchUuid)
 * @method static Builder|Activity forEvent(string $event)
 * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Activity hasBatch()
 * @method static Builder|Activity inLog(...$logNames)
 * @method static Builder|WebhookLog newModelQuery()
 * @method static Builder|WebhookLog newQuery()
 * @method static Builder|WebhookLog query()
 * @method static Builder|WebhookLog whereBatchUuid($value)
 * @method static Builder|WebhookLog whereCauserId($value)
 * @method static Builder|WebhookLog whereCauserType($value)
 * @method static Builder|WebhookLog whereCreatedAt($value)
 * @method static Builder|WebhookLog whereDescription($value)
 * @method static Builder|WebhookLog whereEvent($value)
 * @method static Builder|WebhookLog whereId($value)
 * @method static Builder|WebhookLog whereLogName($value)
 * @method static Builder|WebhookLog whereProperties($value)
 * @method static Builder|WebhookLog whereSubjectId($value)
 * @method static Builder|WebhookLog whereSubjectType($value)
 * @method static Builder|WebhookLog whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class WebhookLog extends Activity
{
    public function data(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('data')
        )->shouldCache();
    }

    public function event(): Attribute
    {
        return Attribute::make(
            get: fn (): Optional|WebhookEvent|null => optional($this->getExtraProperty('event'), fn ($event) => WebhookEvent::from($event))
        )->shouldCache();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('webhook', function (Builder $query) {
            $query->where('log_name', 'webhook');
        });
    }
}
