<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\QualificationRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
trait HasQualificationRecords
{
    public function qualification_records(): HasMany
    {
        return $this->hasMany(QualificationRecord::class);
    }
}
