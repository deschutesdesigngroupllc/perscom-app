<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class StatusData extends Data
{
    public function __construct(
        public string $name,
        public ?string $color = null,
    ) {}
}
