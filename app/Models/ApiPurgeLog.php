<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ApiPurgeLogScope;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;

/**
 * @property int $id
 * @property string|null $log_name
 * @property array<array-key, mixed> $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property \Illuminate\Support\Collection<array-key, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|null $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read string|null $request_id
 * @property-read string $status
 * @property-read \Illuminate\Database\Eloquent\Model|null $subject
 * @property-read array $tags
 * @property-read string|null $trace_id
 * @property-read \Illuminate\Database\Eloquent\Collection|ApiLog[] $apiLog
 * @property-read int|null $api_log_count
 *
 * @method static Builder<static>|ApiPurgeLog causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder<static>|ApiPurgeLog forBatch(string $batchUuid)
 * @method static Builder<static>|ApiPurgeLog forEvent(string $event)
 * @method static Builder<static>|ApiPurgeLog forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|ApiPurgeLog hasBatch()
 * @method static Builder<static>|ApiPurgeLog inLog(...$logNames)
 * @method static Builder<static>|ApiPurgeLog newModelQuery()
 * @method static Builder<static>|ApiPurgeLog newQuery()
 * @method static Builder<static>|ApiPurgeLog query()
 * @method static Builder<static>|ApiPurgeLog whereBatchUuid($value)
 * @method static Builder<static>|ApiPurgeLog whereCauserId($value)
 * @method static Builder<static>|ApiPurgeLog whereCauserType($value)
 * @method static Builder<static>|ApiPurgeLog whereCreatedAt($value)
 * @method static Builder<static>|ApiPurgeLog whereDescription($value)
 * @method static Builder<static>|ApiPurgeLog whereEvent($value)
 * @method static Builder<static>|ApiPurgeLog whereId($value)
 * @method static Builder<static>|ApiPurgeLog whereLogName($value)
 * @method static Builder<static>|ApiPurgeLog whereProperties($value)
 * @method static Builder<static>|ApiPurgeLog whereSubjectId($value)
 * @method static Builder<static>|ApiPurgeLog whereSubjectType($value)
 * @method static Builder<static>|ApiPurgeLog whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[ScopedBy(ApiPurgeLogScope::class)]
class ApiPurgeLog extends Activity
{
    use HasJsonRelationships;

    /**
     * @return BelongsToJson<ApiLog, $this>
     */
    public function apiLog(): BelongsToJson
    {
        return $this->belongsToJson(ApiLog::class, 'properties->request_id', 'properties->request_id');
    }

    /**
     * @return Attribute<string, never>
     */
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Str::headline($this->getExtraProperty('status'))
        )->shouldCache();
    }

    /**
     * @return Attribute<array, never>
     */
    public function tags(): Attribute
    {
        return Attribute::make(
            get: fn (): array => Arr::wrap($this->getExtraProperty('tags'))
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function requestId(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('request_id')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function traceId(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('trace_id')
        )->shouldCache();
    }
}
