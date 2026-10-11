<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class RankData extends Data
{
    public function __construct(
        public string $name,
        public ?string $abbreviation = null,
        public ?string $paygrade = null,
        public ?string $description = null,
        public ?string $category = null,
    ) {}
}
