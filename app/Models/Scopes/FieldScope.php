<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FieldScope implements Scope
{
    /**
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (Gate::check('update', $model) && Auth::check()) {
            $builder->where(function ($query) {
                return $query->visible();
            })->orWhere(function ($query) {
                return $query->hidden();
            });
        } else {
            $builder->visible();
        }
    }
}
