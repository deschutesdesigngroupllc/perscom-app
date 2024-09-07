<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Activity;
use Eloquent;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin Eloquent
 */
trait HasLogs
{
    use LogsActivity;

    public function logs(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontSubmitEmptyLogs()
            ->logFillable();
    }
}
