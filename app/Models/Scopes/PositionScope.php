<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PositionScope implements Scope
{
    /**
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->ordered();
    }
}
