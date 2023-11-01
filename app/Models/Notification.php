<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Notification
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder|Notification user(\App\Models\User $user)
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
