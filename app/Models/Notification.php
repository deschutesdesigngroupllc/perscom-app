<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Notification
 *
 * @property int $id
 * @property string $model_type
 * @property int $model_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Notification whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Notification extends MorphPivot
{
    use HasUser;

    /**
     * @var string
     */
    protected $table = 'model_has_notifications';

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
