<?php

namespace App\Models;

use App\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\LoginToken
 *
 * @property string $token
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 *
 * @method static \Database\Factories\LoginTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken user(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginToken whereUserId($value)
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
     * @var string
     */
    protected $primaryKey = 'token';

    /**
     * @var string[]
     */
    protected $fillable = ['user_id'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->token = $model->token ?? Str::random(128);
        });
    }
}
