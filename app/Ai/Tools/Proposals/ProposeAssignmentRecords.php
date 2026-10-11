<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedOperationData;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Position;
use App\Models\Unit;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\Rule;
use Stringable;

class ProposeAssignmentRecords extends ProposalTool
{
    public function description(): Stringable|string
    {
        return 'Propose assignment records that move one or more members to a unit, optionally with a position and specialty. This only queues the change for the user to review.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            ...$this->memberSchema($schema),
            'unit_id' => $schema->integer()->description('The unit to assign the members to.')->required(),
            'position_id' => $schema->integer()->description('Optional position for the assignment.'),
            'specialty_id' => $schema->integer()->description('Optional specialty for the assignment.'),
            'type' => $schema->string()->enum(collect(AssignmentRecordType::cases())->map->value->all())->description('primary (default) or secondary.'),
            'text' => $schema->string()->description('Optional text for the record.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'position_id' => ['nullable', 'integer', 'exists:positions,id'],
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'type' => ['nullable', Rule::enum(AssignmentRecordType::class)],
            'text' => ['nullable', 'string', 'max:65535'],
        ];
    }

    protected function propose(array $validated): ProposedOperationData
    {
        $type = AssignmentRecordType::tryFrom((string) ($validated['type'] ?? '')) ?? AssignmentRecordType::PRIMARY;
        $position = filled($validated['position_id'] ?? null) ? Position::query()->find($validated['position_id'])?->name : null;

        return new ProposedOperationData(
            type: CopilotOperationType::ASSIGNMENT_RECORD,
            userIds: $validated['user_ids'],
            targetId: (int) $validated['unit_id'],
            text: $validated['text'] ?? null,
            attributes: array_filter([
                'type' => $type->value,
                'position_id' => $validated['position_id'] ?? null,
                'specialty_id' => $validated['specialty_id'] ?? null,
            ], fn (mixed $value): bool => filled($value)),
            summary: sprintf(
                'Assign %s to %s%s (%s)',
                $this->describeMembers($validated['user_ids']),
                Unit::query()->find($validated['unit_id'])?->name,
                filled($position) ? ' as '.$position : '',
                $type->getLabel(),
            ),
        );
    }
}
