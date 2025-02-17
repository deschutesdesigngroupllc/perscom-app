<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\CombatRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasCombatRecords
{
    /**
     * @return HasMany<CombatRecord, TModel>
     */
    public function combat_records(): HasMany
    {
        /** @var TModel $this */
        return $this->hasMany(CombatRecord::class);
    }
}
