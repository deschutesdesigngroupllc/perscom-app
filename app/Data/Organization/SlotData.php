<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class SlotData extends Data
{
    /**
     * @param  string|null  $position  The name of a position defined in the blueprint.
     * @param  string|null  $specialty  The name of a specialty defined in the blueprint.
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $position = null,
        public ?string $specialty = null,
    ) {}
}
