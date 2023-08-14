<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TaskAssignmentScope implements Scope
{
    /**
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (! Gate::check('update', $model) && Auth::check()) {
            $builder->user(Auth::user());
        }
    }
}
