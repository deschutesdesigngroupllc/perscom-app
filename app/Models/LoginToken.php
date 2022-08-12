<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoginToken extends Model
{
    use HasFactory;

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
