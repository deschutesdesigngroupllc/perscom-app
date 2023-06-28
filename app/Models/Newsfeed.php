<?php

namespace App\Models;

use App\Models\Scopes\NewsfeedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Newsfeed extends Activity
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new NewsfeedScope());
    }
}
