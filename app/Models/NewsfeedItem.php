<?php

namespace App\Models;

use App\Models\Scopes\NewsfeedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\NewsfeedItem
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
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NewsfeedItem query()
 *
 * @mixin \Eloquent
 */
class NewsfeedItem extends Activity
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new NewsfeedScope());
    }
}
