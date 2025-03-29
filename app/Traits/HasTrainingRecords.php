<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\TrainingRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasTrainingRecords
{
    /**
     * @return HasMany<TrainingRecord, TModel>
     */
    public function training_records(): HasMany
    {
        /** @var Model $this */
        return $this->hasMany(TrainingRecord::class);
    }
}
