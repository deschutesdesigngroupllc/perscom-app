<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Rank;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class RankScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Rank $builder */
        $builder->ordered();
    }
}
