<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Schedule;
use App\Services\RepeatService;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @mixin Eloquent
 */
trait HasSchedule
{
    public function schedule(): MorphOne
    {
        return $this->morphOne(Schedule::class, 'repeatable');
    }

    public function hasPassed(): Attribute
    {
        return Attribute::make(
            get: function (): bool {
                if (blank($this->last_occurrence)) {
                    return false;
                }

                return $this->last_occurrence->copy()->addMinute()->isPast();
            }
        )->shouldCache();
    }

    public function lastOccurrence(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                if (blank($this->schedule)) {
                    return null;
                }

                return RepeatService::lastOccurrence($this->schedule);
            }
        )->shouldCache();
    }

    public function nextOccurrence(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                if (blank($this->schedule)) {
                    return null;
                }

                return RepeatService::nextOccurrence($this->schedule);
            }
        )->shouldCache();
    }
}
