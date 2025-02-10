<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\ModelLike
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $model
 * @property-read User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModelLike whereUserId($value)
 *
 * @mixin Eloquent
 */
class ModelLike extends MorphPivot
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasUser;

    protected $table = 'model_has_likes';

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
