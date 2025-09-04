<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Award;
use App\Models\AwardRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @mixin Eloquent
 */
trait HasAwardRecords
{
    public function award_records(): HasMany
    {
        return $this->hasMany(AwardRecord::class);
    }

    public function awards(): HasManyThrough
    {
        return $this->hasManyThrough(Award::class, AwardRecord::class, 'user_id', 'id', 'id', 'award_id')
            ->distinct();
    }
}
