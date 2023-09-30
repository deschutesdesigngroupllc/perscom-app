<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AwardRecordScope implements Scope
{
    /**
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->latest();

        if (! Gate::check('view', $model) && Auth::check()) {
            $builder->user(Auth::user());
        }
    }
}
