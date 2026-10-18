<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class AwardData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $category = null,
    ) {}
}
