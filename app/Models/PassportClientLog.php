<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

/**
 * App\Models\PassportClientLog
 *
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property string|null $event
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property string|null $causer_id (DC2Type:guid)
 * @property \Illuminate\Support\Collection|null $properties
 * @property string|null $batch_uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 *
 * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder|Activity forBatch(string $batchUuid)
 * @method static Builder|Activity forEvent(string $event)
 * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Activity hasBatch()
 * @method static Builder|Activity inLog(...$logNames)
 * @method static Builder|PassportClientLog newModelQuery()
 * @method static Builder|PassportClientLog newQuery()
 * @method static Builder|PassportClientLog query()
 * @method static Builder|PassportClientLog whereBatchUuid($value)
 * @method static Builder|PassportClientLog whereCauserId($value)
 * @method static Builder|PassportClientLog whereCauserType($value)
 * @method static Builder|PassportClientLog whereCreatedAt($value)
 * @method static Builder|PassportClientLog whereDescription($value)
 * @method static Builder|PassportClientLog whereEvent($value)
 * @method static Builder|PassportClientLog whereId($value)
 * @method static Builder|PassportClientLog whereLogName($value)
 * @method static Builder|PassportClientLog whereProperties($value)
 * @method static Builder|PassportClientLog whereSubjectId($value)
 * @method static Builder|PassportClientLog whereSubjectType($value)
 * @method static Builder|PassportClientLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PassportClientLog extends Activity
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope('oauth', function (Builder $builder) {
            $builder->where('log_name', '=', 'oauth');
        });
    }
}
