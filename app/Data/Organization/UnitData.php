<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class UnitData extends Data
{
    /**
     * @param  string|null  $parent  The name of the unit this unit sits under, from any group in the blueprint or an existing unit.
     * @param  array<int, SlotData>  $slots
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $parent = null,
        #[DataCollectionOf(SlotData::class)]
        public array $slots = [],
    ) {}
}
