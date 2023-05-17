<?php

namespace App\Traits;

use App\Models\Status;
use App\Models\StatusRecord;

trait HasStatuses
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function statuses()
    {
        return $this->morphToMany(Status::class, 'model', 'model_has_statuses')
            ->withPivot('text', 'created_at')
            ->withTimestamps()
            ->as('record')
            ->orderByPivot('created_at', 'desc')
            ->using(StatusRecord::class);
    }
}
