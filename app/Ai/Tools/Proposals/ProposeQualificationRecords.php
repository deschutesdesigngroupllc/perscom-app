<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedOperationData;
use App\Models\Enums\CopilotOperationType;
use App\Models\Qualification;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Stringable;

class ProposeQualificationRecords extends ProposalTool
{
    public function description(): Stringable|string
    {
        return 'Propose qualification records that grant a qualification to one or more members. This only queues the change for the user to review.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            ...$this->memberSchema($schema),
            'qualification_id' => $schema->integer()->description('The qualification to grant.')->required(),
            'text' => $schema->string()->description('Optional text for the record.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'qualification_id' => ['required', 'integer', 'exists:qualifications,id'],
            'text' => ['nullable', 'string', 'max:65535'],
        ];
    }

    protected function propose(array $validated): ProposedOperationData
    {
        return new ProposedOperationData(
            type: CopilotOperationType::QUALIFICATION_RECORD,
            userIds: $validated['user_ids'],
            targetId: (int) $validated['qualification_id'],
            text: $validated['text'] ?? null,
            summary: sprintf('Grant %s to %s', Qualification::query()->find($validated['qualification_id'])?->name, $this->describeMembers($validated['user_ids'])),
        );
    }
}
