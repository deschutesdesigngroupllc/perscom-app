<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UnitScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Unit $builder */
        $builder->ordered();
    }
}
