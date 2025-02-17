<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Group;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasGroup
{
    public function scopeGroup(Builder $query, Group $group): void
    {
        $query->whereBelongsTo($group);
    }

    /**
     * @return BelongsTo<Group, TModel>
     */
    public function group(): BelongsTo
    {
        /** @var TModel $this */
        return $this->belongsTo(Group::class);
    }

    protected function initializeHasGroup(): void
    {
        $this->mergeFillable([
            'group_id',
        ]);
    }
}
