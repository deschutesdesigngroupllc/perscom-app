<?php

namespace App\Traits;

use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasStatuses
{
    /**
     * Initialize trait
     */
    public function initializeHasStatuses()
    {
        $this->append('status');
    }

    /**
     * @return mixed|null
     */
    public function getStatusAttribute()
    {
        return $this->statuses()
            ->orderByPivot('created_at', 'desc')
            ->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function statuses()
    {
        return $this->morphToMany(Status::class, 'model', 'model_has_statuses')
            ->withPivot(['text'])
            ->withTimestamps()
            ->as('record');
    }
}
