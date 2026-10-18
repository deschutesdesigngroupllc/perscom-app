<?php

declare(strict_types=1);

namespace App\Data\Promotions;

use Spatie\LaravelData\Data;

class RequirementResultData extends Data
{
    public function __construct(
        public string $label,
        public bool $met,
        public string $detail,
    ) {}
}
