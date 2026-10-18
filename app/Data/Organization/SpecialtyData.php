<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class SpecialtyData extends Data
{
    public function __construct(
        public string $name,
        public ?string $abbreviation = null,
        public ?string $description = null,
    ) {}
}
