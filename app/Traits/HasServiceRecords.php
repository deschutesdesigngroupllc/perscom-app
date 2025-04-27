<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\ServiceRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
trait HasServiceRecords
{
    /**
     * @return HasMany<ServiceRecord, $this>
     */
    public function service_records(): HasMany
    {
        return $this->hasMany(ServiceRecord::class);
    }
}
