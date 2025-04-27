<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin Eloquent
 */
trait HasAssignmentRecords
{
    /**
     * @return HasMany<AssignmentRecord, $this>
     */
    public function assignment_records(): HasMany
    {
        return $this->hasMany(AssignmentRecord::class);
    }

    public function primary_assignment_records(): HasMany
    {
        return $this->assignment_records()->where('type', AssignmentRecordType::PRIMARY);
    }

    public function secondary_assignment_records(): HasMany
    {
        return $this->assignment_records()->where('type', AssignmentRecordType::SECONDARY);
    }
}
