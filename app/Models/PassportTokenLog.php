<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

/**
 * App\Models\PassportTokenLog
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
 * @method static Builder|PassportTokenLog newModelQuery()
 * @method static Builder|PassportTokenLog newQuery()
 * @method static Builder|PassportTokenLog query()
 * @method static Builder|PassportTokenLog whereBatchUuid($value)
 * @method static Builder|PassportTokenLog whereCauserId($value)
 * @method static Builder|PassportTokenLog whereCauserType($value)
 * @method static Builder|PassportTokenLog whereCreatedAt($value)
 * @method static Builder|PassportTokenLog whereDescription($value)
 * @method static Builder|PassportTokenLog whereEvent($value)
 * @method static Builder|PassportTokenLog whereId($value)
 * @method static Builder|PassportTokenLog whereLogName($value)
 * @method static Builder|PassportTokenLog whereProperties($value)
 * @method static Builder|PassportTokenLog whereSubjectId($value)
 * @method static Builder|PassportTokenLog whereSubjectType($value)
 * @method static Builder|PassportTokenLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PassportTokenLog extends Activity
{
    use HasFactory;

    protected static function booted(): void
    {
        static::addGlobalScope('api', function (Builder $builder) {
            $builder->where('log_name', '=', 'api');
        });
    }
}
