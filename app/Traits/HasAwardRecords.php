<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AwardRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
trait HasAwardRecords
{
    public function award_records(): HasMany
    {
        return $this->hasMany(AwardRecord::class);
    }
}
