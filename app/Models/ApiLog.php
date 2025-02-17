<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\ApiLogScope;
use Eloquent;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
 * @property-read mixed|null $body
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent|null $causer
 * @property-read mixed|null $content
 * @property-read string|null $endpoint
 * @property-read mixed|null $files
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read string|null $ip_address
 * @property-read string|null $method
 * @property-read mixed|null $request_headers
 * @property-read string|null $request_id
 * @property-read mixed|null $response_headers
 * @property-read string|int|null|null $status
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent|null $subject
 * @property-read string|null $trace_id
 *
 * @method static Builder<static>|ApiLog causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder<static>|ApiLog forBatch(string $batchUuid)
 * @method static Builder<static>|ApiLog forEvent(string $event)
 * @method static Builder<static>|ApiLog forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder<static>|ApiLog hasBatch()
 * @method static Builder<static>|ApiLog inLog(...$logNames)
 * @method static Builder<static>|ApiLog newModelQuery()
 * @method static Builder<static>|ApiLog newQuery()
 * @method static Builder<static>|ApiLog query()
 * @method static Builder<static>|ApiLog whereBatchUuid($value)
 * @method static Builder<static>|ApiLog whereCauserId($value)
 * @method static Builder<static>|ApiLog whereCauserType($value)
 * @method static Builder<static>|ApiLog whereCreatedAt($value)
 * @method static Builder<static>|ApiLog whereDescription($value)
 * @method static Builder<static>|ApiLog whereEvent($value)
 * @method static Builder<static>|ApiLog whereId($value)
 * @method static Builder<static>|ApiLog whereLogName($value)
 * @method static Builder<static>|ApiLog whereProperties($value)
 * @method static Builder<static>|ApiLog whereSubjectId($value)
 * @method static Builder<static>|ApiLog whereSubjectType($value)
 * @method static Builder<static>|ApiLog whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
#[ScopedBy(ApiLogScope::class)]
class ApiLog extends Activity
{
    /**
     * @return Attribute<?string, never>
     */
    public function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('ip')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function method(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('method')
        )->shouldCache();
    }

    /**
     * @return Attribute<?string, never>
     */
    public function endpoint(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('endpoint')
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
    public function files(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('files')
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
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn (): string|int|null => $this->getExtraProperty('status')
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

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), parent::getCasts(), [
            'request_headers' => 'array',
            'response_headers' => 'array',
        ]);
    }
}
