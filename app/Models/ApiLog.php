<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ApiLogScope;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\HasManyJson;
use Zoha\Metable;

/**
 * @property int $id
 * @property string|null $log_name
 * @property array<array-key, mixed> $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property Collection<array-key, mixed>|null $properties
 * @property string|null $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed|null $body
 * @property-read Model|null $causer
 * @property-read mixed|null $content
 * @property-read string|int|null|null $duration
 * @property-read string|null $endpoint
 * @property-read Collection $changes
 * @property-read string|null $ip_address
 * @property-read string|null $method
 * @property-read mixed|null $request_headers
 * @property-read string|null $request_id
 * @property-read mixed|null $response_headers
 * @property-read string|int|null|null $status
 * @property-read Model|null $subject
 * @property-read string|null $trace_id
 * @property-read \Illuminate\Database\Eloquent\Collection|ApiPurgeLog[] $purges
 * @property-read int|null $purges_count
 *
 * @method static Builder<static>|ApiLog causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder<static>|ApiLog forBatch(string $batchUuid)
 * @method static Builder<static>|ApiLog forEvent(string $event)
 * @method static Builder<static>|ApiLog forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|ApiLog hasBatch()
 * @method static Builder<static>|ApiLog inLog(...$logNames)
 * @method static Builder<static>|ApiLog newModelQuery()
 * @method static Builder<static>|ApiLog newQuery()
 * @method static Builder<static>|ApiLog orWhereMeta($key, $operator = null, $value = null)
 * @method static Builder<static>|ApiLog orWhereMetaBetween($key, $values = [])
 * @method static Builder<static>|ApiLog orWhereMetaDoesntHave($key = null, $countNull = false, $type = null)
 * @method static Builder<static>|ApiLog orWhereMetaHas($key = null, $countNull = false, $type = null)
 * @method static Builder<static>|ApiLog orWhereMetaIn($key, $values = [])
 * @method static Builder<static>|ApiLog orWhereMetaNotBetween($key, $values = [])
 * @method static Builder<static>|ApiLog orWhereMetaNotIn($key, $values = [])
 * @method static Builder<static>|ApiLog orWhereMetaNotNull($key)
 * @method static Builder<static>|ApiLog orWhereMetaNull($key)
 * @method static Builder<static>|ApiLog orderByMeta(string $key, string $direction = 'asc')
 * @method static Builder<static>|ApiLog query()
 * @method static Builder<static>|ApiLog whereBatchUuid($value)
 * @method static Builder<static>|ApiLog whereCauserId($value)
 * @method static Builder<static>|ApiLog whereCauserType($value)
 * @method static Builder<static>|ApiLog whereCreatedAt($value)
 * @method static Builder<static>|ApiLog whereDescription($value)
 * @method static Builder<static>|ApiLog whereEvent($value)
 * @method static Builder<static>|ApiLog whereId($value)
 * @method static Builder<static>|ApiLog whereLogName($value)
 * @method static Builder<static>|ApiLog whereMeta($key, $operator = 'NOVALUEFORPARAMETER', $value = 'NOVALUEFORPARAMETER', $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaBetween($key, $values = [], $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaDoesntHave($key = null, $countNull = false, $type = null, $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaHas($key = null, $countNull = false, $type = null, $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaIn($key, $values = [], $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaNotBetween($key, $values = [], $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaNotIn($key, $values = [], $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaNotNull($key, $orWhere = false)
 * @method static Builder<static>|ApiLog whereMetaNull($key, $orWhere = false)
 * @method static Builder<static>|ApiLog whereProperties($value)
 * @method static Builder<static>|ApiLog whereSubjectId($value)
 * @method static Builder<static>|ApiLog whereSubjectType($value)
 * @method static Builder<static>|ApiLog whereUpdatedAt($value)
 * @method static Builder<static>|ApiLog withMeta()
 *
 * @mixin Eloquent
 */
#[ScopedBy(ApiLogScope::class)]
class ApiLog extends Activity
{
    use HasJsonRelationships;
    use Metable;

    /**
     * @return Attribute<?string, never>
     */
    public function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getMeta('ip')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function method(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getMeta('method')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function endpoint(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getMeta('endpoint')
        )->shouldCache();
    }

    /**
     * @return Attribute<mixed, never>
     */
    public function body(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('body')
        )->shouldCache();
    }

    /**
     * @return Attribute<mixed, never>
     */
    public function content(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('content')
        )->shouldCache();
    }

    /**
     * @return Attribute<string|int|null, never>
     */
    public function duration(): Attribute
    {
        return Attribute::make(
            get: fn (): string|int|null => $this->getMeta('duration')
        )->shouldCache();
    }

    /**
     * @return Attribute<string|int|null, never>
     */
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn (): string|int|null => $this->getMeta('status')
        )->shouldCache();
    }

    /**
     * @return Attribute<mixed, never>
     */
    public function requestHeaders(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('request_headers')
        )->shouldCache();
    }

    /**
     * @return Attribute<mixed, never>
     */
    public function responseHeaders(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('response_headers')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function requestId(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getMeta('request_id')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function traceId(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getMeta('trace_id')
        )->shouldCache();
    }

    /**
     * @return HasManyJson<ApiPurgeLog, $this>
     */
    public function purges(): HasManyJson
    {
        return $this->hasManyJson(ApiPurgeLog::class, 'properties->request_id', 'properties->request_id');
    }

    protected function casts(): array
    {
        return array_merge(parent::casts(), parent::getCasts(), [
            'request_headers' => 'array',
            'response_headers' => 'array',
            'duration' => 'integer',
            'status' => 'integer',
        ]);
    }
}
