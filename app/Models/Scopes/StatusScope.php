<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class StatusScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Status $builder */
        $builder->ordered();
    }
}
