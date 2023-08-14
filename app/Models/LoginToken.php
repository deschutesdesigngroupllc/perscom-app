<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\LoginToken
 *
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\LoginTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken user(\App\Models\User $user)
 *
 * @mixin \Eloquent
 */
class LoginToken extends Model
{
    use HasFactory;
    use HasUser;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'token';

    /**
     * @var string[]
     */
    protected $fillable = ['user_id'];

    /**
     * Boot method
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->token = $model->token ?? Str::random(128);
        });
    }
}
