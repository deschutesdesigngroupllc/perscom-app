<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use App\Models\Slot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SlotScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        /** @var Slot $builder */
        $builder->ordered();
    }
}
