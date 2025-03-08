<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Schedule;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasSchedule
{
    /**
     * @return MorphOne<Schedule, TModel>
     */
    public function schedule(): MorphOne
    {
        /** @var TModel $this */
        return $this->morphOne(Schedule::class, 'repeatable');
    }

    protected static function bootHasSchedule(): void
    {
        static::deleted(function ($model): void {
            if (filled($model->schedule)) {
                $model->schedule()->delete();
            }
        });
    }
}
