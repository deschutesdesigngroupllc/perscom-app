<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SpecialtyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Specialty $builder */
        $builder->ordered();
    }
}
