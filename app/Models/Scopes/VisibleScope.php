<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Contracts\Hideable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisibleScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Hideable $builder */
        $builder->visible();
    }
}
