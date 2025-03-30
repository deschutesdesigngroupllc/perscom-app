<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Qualification;
use App\Models\QualificationRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasQualificationRecords
{
    /**
     * @return HasMany<QualificationRecord, TModel>
     */
    public function qualification_records(): HasMany
    {
        /** @var TModel $this */
        return $this->hasMany(QualificationRecord::class);
    }

    /**
     * @return HasManyThrough<Qualification, TModel>
     */
    public function qualifications(): HasManyThrough
    {
        /** @var TModel $this */
        return $this->hasManyThrough(Qualification::class, QualificationRecord::class, 'user_id', 'id', 'id', 'qualification_id')
            ->distinct();
    }
}
