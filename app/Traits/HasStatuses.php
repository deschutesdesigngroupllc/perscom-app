<?php

namespace App\Traits;

use App\Models\Status;
use App\Models\StatusRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasStatuses
{
    public function statuses(): MorphToMany
    {
        return $this->morphToMany(Status::class, 'model', 'model_has_statuses')
            ->withPivot('text', 'created_at')
            ->withTimestamps()
            ->as('record')
            ->orderByPivot('created_at', 'desc')
            ->using(StatusRecord::class);
    }

    public function scopeStatus(Builder $query, mixed $statuses): void
    {
        if ($statuses instanceof Collection) {
            $statuses = $statuses->all();
        }

        $statuses = Arr::map(Arr::wrap($statuses), function ($status) {
            if ($status instanceof Status) {
                return $status;
            }

            $column = is_numeric($status) ? 'id' : 'name';

            return Status::query()->where($column, '=', $status)->first();
        });

        $query->whereHas('statuses', function (Builder $query) use ($statuses) {
            $query->whereIn('status_id', array_column($statuses, 'id'));
        });
    }
}
