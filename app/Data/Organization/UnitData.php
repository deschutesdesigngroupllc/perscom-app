<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class UnitData extends Data
{
    /**
     * @param  array<int, SlotData>  $slots
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        #[DataCollectionOf(SlotData::class)]
        public array $slots = [],
    ) {}
}
