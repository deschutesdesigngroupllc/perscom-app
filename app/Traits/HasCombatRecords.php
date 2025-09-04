<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\CombatRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
trait HasCombatRecords
{
    public function combat_records(): HasMany
    {
        return $this->hasMany(CombatRecord::class);
    }
}
