<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property string|null $log_name
 * @property array $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id
 * @property \Illuminate\Support\Collection|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed|null $body
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $causer
 * @property-read mixed|null $content
 * @property-read string|null $endpoint
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read string|null $ip_address
 * @property-read string|null $method
 * @property-read mixed|null $request_headers
 * @property-read mixed|null $response_headers
 * @property-read string|int|null|null $status
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $subject
 *
 * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder|Activity forBatch(string $batchUuid)
 * @method static Builder|Activity forEvent(string $event)
 * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Activity hasBatch()
 * @method static Builder|Activity inLog(...$logNames)
 * @method static Builder|ApiLog newModelQuery()
 * @method static Builder|ApiLog newQuery()
 * @method static Builder|ApiLog query()
 * @method static Builder|ApiLog whereBatchUuid($value)
 * @method static Builder|ApiLog whereCauserId($value)
 * @method static Builder|ApiLog whereCauserType($value)
 * @method static Builder|ApiLog whereCreatedAt($value)
 * @method static Builder|ApiLog whereDescription($value)
 * @method static Builder|ApiLog whereEvent($value)
 * @method static Builder|ApiLog whereId($value)
 * @method static Builder|ApiLog whereLogName($value)
 * @method static Builder|ApiLog whereProperties($value)
 * @method static Builder|ApiLog whereSubjectId($value)
 * @method static Builder|ApiLog whereSubjectType($value)
 * @method static Builder|ApiLog whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class ApiLog extends Activity
{
    public function ipAddress(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('ip')
        )->shouldCache();
    }

    public function method(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('method')
        )->shouldCache();
    }

    public function endpoint(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getExtraProperty('endpoint')
        )->shouldCache();
    }

    public function body(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('body')
        )->shouldCache();
    }

    public function content(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('content')
        )->shouldCache();
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: fn (): string|int|null => $this->getExtraProperty('status')
        )->shouldCache();
    }

    public function requestHeaders(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('request_headers')
        )->shouldCache();
    }

    public function responseHeaders(): Attribute
    {
        return Attribute::make(
            get: fn (): mixed => $this->getExtraProperty('response_headers')
        )->shouldCache();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('api', function (Builder $query) {
            $query->whereIn('log_name', ['api', 'jwt', 'oauth']);
        });
    }

    protected function casts(): array
    {
        return array_merge(parent::casts(), parent::getCasts(), [
            'request_headers' => 'array',
            'response_headers' => 'array',
        ]);
    }
}
