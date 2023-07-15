<?php

namespace App\Traits;

use App\Models\Status;
use App\Models\StatusRecord;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
}
