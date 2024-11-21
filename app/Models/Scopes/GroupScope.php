<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class GroupScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Group $builder */
        $builder->ordered();
    }
}
