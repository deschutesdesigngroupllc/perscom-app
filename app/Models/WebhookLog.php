<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Enums\WebhookEvent;
use App\Models\Scopes\WebhookLogScope;
use Eloquent;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property string|null $log_name
 * @property array<array-key, mixed> $description
 * @property string|null $subject_type
 * @property WebhookEvent|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent|null $causer
 * @property-read mixed|null $data
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read mixed|null $request_id
 * @property-read string|null $resource_url
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent|null $subject
 * @property-read mixed|null $trace_id
 *
 * @method static Builder<static>|WebhookLog causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder<static>|WebhookLog forBatch(string $batchUuid)
 * @method static Builder<static>|WebhookLog forEvent(string $event)
 * @method static Builder<static>|WebhookLog forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|WebhookLog hasBatch()
 * @method static Builder<static>|WebhookLog inLog(...$logNames)
 * @method static Builder<static>|WebhookLog newModelQuery()
 * @method static Builder<static>|WebhookLog newQuery()
 * @method static Builder<static>|WebhookLog query()
 * @method static Builder<static>|WebhookLog whereBatchUuid($value)
 * @method static Builder<static>|WebhookLog whereCauserId($value)
 * @method static Builder<static>|WebhookLog whereCauserType($value)
 * @method static Builder<static>|WebhookLog whereCreatedAt($value)
 * @method static Builder<static>|WebhookLog whereDescription($value)
 * @method static Builder<static>|WebhookLog whereEvent($value)
 * @method static Builder<static>|WebhookLog whereId($value)
 * @method static Builder<static>|WebhookLog whereLogName($value)
 * @method static Builder<static>|WebhookLog whereProperties($value)
 * @method static Builder<static>|WebhookLog whereSubjectId($value)
 * @method static Builder<static>|WebhookLog whereSubjectType($value)
 * @method static Builder<static>|WebhookLog whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[ScopedBy(WebhookLogScope::class)]
class WebhookLog extends Activity
{
    /**
     * @return Attribute<mixed, void>
     */
    public function data(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('data')
        )->shouldCache();
    }

    /**
     * @return Attribute<?WebhookEvent, void>
     */
    public function event(): Attribute
    {
        return Attribute::make(
            get: fn (): ?WebhookEvent => optional($this->getExtraProperty('event'), fn ($event) => WebhookEvent::from($event))
        )->shouldCache();
    }

    public function requestId(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('request_id')
        )->shouldCache();
    }

    public function traceId(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('trace_id')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, void>
     */
    public function resourceUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            /** @var resource $resource */
            $resource = Filament::getModelResource($this->causer);

            if (blank($resource)) {
                return null;
            }

            if (array_key_exists('view', $resource::getPages())) {
                return $resource::getUrl('view', [
                    'record' => $this->causer,
                ]);
            }

            if (array_key_exists('edit', $resource::getPages())) {
                return $resource::getUrl('edit', [
                    'record' => $this->causer,
                ]);
            }

            return null;
        })->shouldCache();
    }
}
