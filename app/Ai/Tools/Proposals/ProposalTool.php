<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\ValidationException;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Proposal tools never write to the database. They add an operation to the change set,
 * which the user reviews and explicitly applies afterwards.
 */
abstract class ProposalTool implements Tool
{
    public const int MAX_MEMBERS = 200;

    public function __construct(protected ProposedChangeSetData $changeSet) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    abstract protected function propose(array $validated): ProposedOperationData;

    /**
     * @return array<string, mixed>
     */
    abstract protected function rules(): array;

    public function handle(Request $request): Stringable|string
    {
        try {
            $validated = $request->validate([
                'user_ids' => ['required', 'array', 'min:1', 'max:'.static::MAX_MEMBERS],
                'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
                ...$this->rules(),
            ]);
        } catch (ValidationException $validationException) {
            return 'The proposal was not added: '.collect($validationException->errors())->flatten()->implode(' ');
        }

        $operation = $this->propose($validated);

        $this->changeSet->add($operation);

        return 'Proposed for review (nothing has been changed yet): '.$operation->summary;
    }

    /**
     * @return array<string, mixed>
     */
    protected function memberSchema(JsonSchema $schema): array
    {
        return [
            'user_ids' => $schema->array()->items($schema->integer())->description('The ids of the members this applies to.')->required(),
        ];
    }

    /**
     * @param  array<int, int>  $userIds
     */
    protected function describeMembers(array $userIds): string
    {
        $names = User::query()->whereKey($userIds)->orderBy('name')->limit(3)->pluck('name');
        $remaining = count($userIds) - $names->count();

        return $names->implode(', ').($remaining > 0 ? sprintf(' and %d more', $remaining) : '');
    }
}
