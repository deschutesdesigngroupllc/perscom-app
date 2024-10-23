<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Schedule;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Eloquent
 */
trait HasSchedule
{
    public function schedule(): MorphOne
    {
        return $this->morphOne(Schedule::class, 'repeatable');
    }
}
