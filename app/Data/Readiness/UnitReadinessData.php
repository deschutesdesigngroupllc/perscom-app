<?php

declare(strict_types=1);

namespace App\Data\Readiness;

use App\Models\Unit;
use Spatie\LaravelData\Data;

class UnitReadinessData extends Data
{
    public function __construct(
        public ?Unit $unit,
        public string $label,
        public int $members,
        public ?float $readyPercentage,
        public ?float $attendanceRate,
        public int $expiring,
    ) {}
}
