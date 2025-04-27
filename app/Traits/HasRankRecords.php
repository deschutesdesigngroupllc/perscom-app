<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\RankRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
trait HasRankRecords
{
    /**
     * @return HasMany<RankRecord, $this>
     */
    public function rank_records(): HasMany
    {
        return $this->hasMany(RankRecord::class);
    }
}
