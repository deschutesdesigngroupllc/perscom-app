<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder
            ->leftJoin('ranks', 'ranks.id', '=', 'users.rank_id')
            ->leftJoin('positions', 'positions.id', '=', 'users.position_id')
            ->leftJoin('specialties', 'specialties.id', '=', 'users.specialty_id')
            ->orderBy('ranks.order')
            ->orderBy('positions.order')
            ->orderBy('specialties.order')
            ->orderBy('users.name');
    }
}
