<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Qualification;
use App\Models\QualificationRecord;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @mixin Eloquent
 */
trait HasQualificationRecords
{
    public function qualification_records(): HasMany
    {
        return $this->hasMany(QualificationRecord::class);
    }

    public function qualifications(): HasManyThrough
    {
        return $this->hasManyThrough(Qualification::class, QualificationRecord::class, 'user_id', 'id', 'id', 'qualification_id')
            ->distinct();
    }
}
