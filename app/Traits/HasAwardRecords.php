<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Award;
use App\Models\AwardRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasAwardRecords
{
    /**
     * @return HasMany<AwardRecord, TModel>
     */
    public function award_records(): HasMany
    {
        /** @var TModel $this */
        return $this->hasMany(AwardRecord::class);
    }

    /**
     * @return HasManyThrough<Award, TModel>
     */
    public function awards(): HasManyThrough
    {
        /** @var TModel $this */
        return $this->hasManyThrough(Award::class, AwardRecord::class, 'user_id', 'id', 'id', 'award_id')
            ->distinct();
    }
}
