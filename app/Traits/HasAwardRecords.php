<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AwardRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
