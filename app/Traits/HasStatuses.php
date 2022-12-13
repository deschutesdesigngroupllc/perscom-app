<?php

namespace App\Traits;

use App\Models\Status;
use App\Models\Records\Status as StatusRecord;

trait HasStatuses
{
	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function statuses()
    {
        return $this->morphToMany(Status::class, 'model', 'model_has_statuses')
            ->withPivot('text')
            ->withTimestamps()
	        ->as('record')
            ->using(StatusRecord::class);
    }
}
