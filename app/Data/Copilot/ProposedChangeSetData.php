<?php

declare(strict_types=1);

namespace App\Data\Copilot;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class ProposedChangeSetData extends Data
{
    /**
     * @param  array<int, ProposedOperationData>  $operations
     */
    public function __construct(
        #[DataCollectionOf(ProposedOperationData::class)]
        public array $operations = [],
    ) {}

    public function add(ProposedOperationData $operation): static
    {
        $this->operations[] = $operation;

        return $this;
    }

    public function isEmpty(): bool
    {
        return blank($this->operations);
    }

    /**
     * @return array<int, string>
     */
    public function summaries(): array
    {
        return collect($this->operations)
            ->map(fn (ProposedOperationData $operation): string => $operation->summary)
            ->all();
    }
}
