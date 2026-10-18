<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class PositionData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description = null,
    ) {}
}
