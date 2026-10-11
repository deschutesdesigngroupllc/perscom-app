<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class GroupData extends Data
{
    /**
     * @param  string|null  $icon  A Heroicon name such as heroicon-o-fire.
     * @param  array<int, UnitData>  $units
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $icon = null,
        #[DataCollectionOf(UnitData::class)]
        public array $units = [],
    ) {}
}
