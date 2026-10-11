<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedOperationData;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\Rule;
use Stringable;

class ProposeRankRecords extends ProposalTool
{
    public function description(): Stringable|string
    {
        return 'Propose rank records (promotions, demotions, lateral moves or transfers) for one or more members. This only queues the change for the user to review.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            ...$this->memberSchema($schema),
            'rank_id' => $schema->integer()->description('The rank to record.')->required(),
            'type' => $schema->integer()->enum(collect(RankRecordType::cases())->map->value->all())->description('0 = promotion (default), 1 = demotion, 2 = lateral, 3 = transfer.'),
            'text' => $schema->string()->description('Optional text for the record, such as the reason.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'rank_id' => ['required', 'integer', 'exists:ranks,id'],
            'type' => ['nullable', Rule::enum(RankRecordType::class)],
            'text' => ['nullable', 'string', 'max:65535'],
        ];
    }

    protected function propose(array $validated): ProposedOperationData
    {
        $type = RankRecordType::from((int) ($validated['type'] ?? RankRecordType::PROMOTION->value));

        return new ProposedOperationData(
            type: CopilotOperationType::RANK_RECORD,
            userIds: $validated['user_ids'],
            targetId: (int) $validated['rank_id'],
            text: $validated['text'] ?? null,
            attributes: ['type' => $type->value],
            summary: sprintf('%s of %s to %s', $type->getLabel(), $this->describeMembers($validated['user_ids']), Rank::query()->find($validated['rank_id'])?->name),
        );
    }
}
