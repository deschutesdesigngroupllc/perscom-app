<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class FormData extends Data
{
    /**
     * @param  array<int, FieldData>  $fields
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?string $instructions = null,
        public ?string $successMessage = null,
        public bool $isPublic = false,
        #[DataCollectionOf(FieldData::class)]
        public array $fields = [],
    ) {}
}
