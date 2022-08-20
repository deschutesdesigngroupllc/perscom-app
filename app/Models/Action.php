<?php

namespace App\Models;

use App\Models\Passport\Token;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\ActionEvent;

class Action extends ActionEvent
{
    use HasFactory;

    /**
     * Bootstrap the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        parent::saving(function ($model) {
            if (\in_array($model->actionable_type, [\Laravel\Passport\Client::class, Token::class], true)) {
                return false;
            }
        });
    }
}
