<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

/**
 * App\Models\PassportTokenLog
 *
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
