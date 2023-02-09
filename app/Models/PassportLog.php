<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

/**
 * App\Models\PassportLog
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
 * @method static \Illuminate\Database\Eloquent\Builder|PassportLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PassportLog query()
 * @mixin \Eloquent
 */
class PassportLog extends Activity
{
    use HasFactory;
}
