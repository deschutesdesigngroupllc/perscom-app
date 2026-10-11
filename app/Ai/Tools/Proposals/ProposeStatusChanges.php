<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedOperationData;
use App\Models\Enums\CopilotOperationType;
use App\Models\Status;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Stringable;

class ProposeStatusChanges extends ProposalTool
{
    public function description(): Stringable|string
    {
        return 'Propose changing the status of one or more members, for example to Inactive or On Leave. This only queues the change for the user to review.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            ...$this->memberSchema($schema),
            'status_id' => $schema->integer()->description('The new status.')->required(),
            'text' => $schema->string()->description('Optional reason for the change.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'text' => ['nullable', 'string', 'max:65535'],
        ];
    }

    protected function propose(array $validated): ProposedOperationData
    {
        return new ProposedOperationData(
            type: CopilotOperationType::STATUS_RECORD,
            userIds: $validated['user_ids'],
            targetId: (int) $validated['status_id'],
            text: $validated['text'] ?? null,
            summary: sprintf('Set %s to %s', $this->describeMembers($validated['user_ids']), Status::query()->find($validated['status_id'])?->name),
        );
    }
}
