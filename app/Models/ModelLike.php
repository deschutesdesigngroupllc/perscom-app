<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasUser;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ModelLike
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|Eloquent $model
 * @property-read User $user
 *
 * @method static Builder<static>|ModelLike newModelQuery()
 * @method static Builder<static>|ModelLike newQuery()
 * @method static Builder<static>|ModelLike query()
 * @method static Builder<static>|ModelLike user(User $user)
 * @method static Builder<static>|ModelLike whereCreatedAt($value)
 * @method static Builder<static>|ModelLike whereId($value)
 * @method static Builder<static>|ModelLike whereModelId($value)
 * @method static Builder<static>|ModelLike whereModelType($value)
 * @method static Builder<static>|ModelLike whereUpdatedAt($value)
 * @method static Builder<static>|ModelLike whereUserId($value)
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
