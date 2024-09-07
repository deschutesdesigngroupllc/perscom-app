<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class NewsfeedScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('log_name', 'newsfeed')->latest();
    }
}
