<?php

declare(strict_types=1);

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class FieldScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (Gate::check('update', $model) && Auth::check()) {
            $builder->where(function ($query) {
                return $query->visible(); // @phpstan-ignore-line
            })->orWhere(function ($query) {
                return $query->hidden(); // @phpstan-ignore-line
            });
        } else {
            $builder->visible(); // @phpstan-ignore-line
        }
    }
}
