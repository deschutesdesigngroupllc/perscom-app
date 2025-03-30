<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Credential;
use App\Models\TrainingRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasTrainingRecords
{
    use HasRelationships;

    /**
     * @return HasMany<TrainingRecord, TModel>
     */
    public function training_records(): HasMany
    {
        /** @var TModel $this */
        return $this->hasMany(TrainingRecord::class);
    }

    /**
     * @return HasManyDeep<Credential, TModel>
     */
    public function credentials(): HasManyDeep
    {
        /** @var TModel $this */
        return $this->hasManyDeep(
            Credential::class,
            [TrainingRecord::class, 'records_trainings_credentials'],
        );
    }
}
