<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
trait CanBeRead
{
    public function scopeRead(Builder $query): void
    {
        $query->whereNotNull('read_at');
    }

    public function scopeUnread(Builder $query): void
    {
        $query->whereNull('read_at');
    }

    public function markAsRead(): static
    {
        return tap($this, fn (Model $model) => $model->update(['read_at' => now()]));
    }

    public function markAsUnread(): static
    {
        return tap($this, fn (Model $model) => $model->update(['read_at' => null]));
    }

    protected function initializeCanBeHidden(): void
    {
        $this->mergeFillable(['read_at']);

        $this->mergeCasts([
            'read_at' => 'boolean',
        ]);
    }
}
