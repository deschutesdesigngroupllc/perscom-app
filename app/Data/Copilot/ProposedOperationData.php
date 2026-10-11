<?php

declare(strict_types=1);

namespace App\Data\Copilot;

use App\Models\Enums\CopilotOperationType;
use Spatie\LaravelData\Data;

class ProposedOperationData extends Data
{
    /**
     * @param  array<int, int>  $userIds  The members the operation applies to. For messages, these are the recipients.
     * @param  int|null  $targetId  The rank, unit, award, qualification or status the operation points at. Unused for messages.
     * @param  string|null  $text  The record text, or the message body for messages.
     * @param  array<string, mixed>  $attributes  Operation specific values, such as the rank record type or the assignment position.
     * @param  string  $summary  A human-readable description of the operation.
     */
    public function __construct(
        public CopilotOperationType $type,
        public array $userIds,
        public ?int $targetId = null,
        public ?string $text = null,
        public array $attributes = [],
        public string $summary = '',
    ) {}
}
