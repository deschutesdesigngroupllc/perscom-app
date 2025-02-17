<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ServiceRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 *
 * @template TModel of Model
 */
trait HasServiceRecords
{
    /**
     * @return HasMany<ServiceRecord, TModel>
     */
    public function service_records(): HasMany
    {
        /** @var TModel $this */
        return $this->hasMany(ServiceRecord::class);
    }
}
