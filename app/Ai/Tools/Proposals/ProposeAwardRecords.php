<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedOperationData;
use App\Models\Award;
use App\Models\Enums\CopilotOperationType;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Stringable;

class ProposeAwardRecords extends ProposalTool
{
    public function description(): Stringable|string
    {
        return 'Propose award records that give an award to one or more members. This only queues the change for the user to review.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            ...$this->memberSchema($schema),
            'award_id' => $schema->integer()->description('The award to give.')->required(),
            'text' => $schema->string()->description('Optional citation text.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'award_id' => ['required', 'integer', 'exists:awards,id'],
            'text' => ['nullable', 'string', 'max:65535'],
        ];
    }

    protected function propose(array $validated): ProposedOperationData
    {
        return new ProposedOperationData(
            type: CopilotOperationType::AWARD_RECORD,
            userIds: $validated['user_ids'],
            targetId: (int) $validated['award_id'],
            text: $validated['text'] ?? null,
            summary: sprintf('Award %s to %s', Award::query()->find($validated['award_id'])?->name, $this->describeMembers($validated['user_ids'])),
        );
    }
}
