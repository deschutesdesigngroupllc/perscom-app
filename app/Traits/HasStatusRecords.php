<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Status;
use App\Models\StatusRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @mixin Eloquent
 */
trait HasStatusRecords
{
    public function statuses(): MorphToMany
    {
        return $this->morphToMany(Status::class, 'model', StatusRecord::class)
            ->withPivot('text', 'created_at')
            ->withTimestamps()
            ->as('record')
            ->orderByPivot('created_at', 'desc')
            ->using(StatusRecord::class);
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (): Model|Status|null => $this->statuses()->first()
        );
    }
}
