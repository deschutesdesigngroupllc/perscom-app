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

    protected static function bootHasSchedule(): void
    {
        static::deleted(function ($model) {
            if (filled($model->schedule)) {
                $model->schedule()->delete();
            }
        });
    }
}
