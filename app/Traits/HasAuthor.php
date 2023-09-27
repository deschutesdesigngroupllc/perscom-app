<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasAuthor
{
    /**
     * Run on boot
     */
    public static function bootHasAuthor()
    {
        static::creating(function ($model) {
            if ($user = Auth::user() && ! $model->author_id) {
                $model->author()->associate($user);
            }
        });
    }

    /**
     * @param  Builder  $query
     * @param  User  $user
     * @return Builder
     */
    public function scopeAuthor($query, $user)
    {
        return $query->whereBelongsTo($user);
    }

    /**
     * @return mixed
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
