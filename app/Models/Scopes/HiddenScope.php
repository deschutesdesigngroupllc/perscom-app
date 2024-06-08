<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HiddenScope implements Scope
{
    public function apply(Builder $query, Model $model): void
    {
        $query->hidden();
    }
}
