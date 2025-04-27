<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Credential;
use App\Models\TrainingRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin Eloquent
 */
trait HasTrainingRecords
{
    use HasRelationships;

    /**
     * @return HasMany<TrainingRecord, $this>
     */
    public function training_records(): HasMany
    {
        return $this->hasMany(TrainingRecord::class);
    }

    /**
     * @return HasManyDeep<Credential, $this>
     */
    public function credentials(): HasManyDeep
    {
        return $this->hasManyDeep(
            Credential::class,
            [TrainingRecord::class, 'records_trainings_credentials'],
        );
    }
}
