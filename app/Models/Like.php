<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Like
 *
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $model
 * @property-read \App\Models\User|null $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|Like user(\App\Models\User $user)
 *
 * @mixin \Eloquent
 */
class Like extends MorphPivot
{
    use HasUser;

    /**
     * @var string
     */
    protected $table = 'model_has_likes';

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
