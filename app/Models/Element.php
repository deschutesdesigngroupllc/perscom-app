<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CanBeOrdered;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\EloquentSortable\Sortable;

/**
 * App\Models\Element
 *
 * @property int $id
 * @property int $field_id
 * @property string $model_type
 * @property int $model_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $model
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Element whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Element extends MorphPivot implements Sortable
{
    use CanBeOrdered;
    use ClearsApiCache;
    use ClearsResponseCache;

    protected $table = 'model_has_fields';

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
