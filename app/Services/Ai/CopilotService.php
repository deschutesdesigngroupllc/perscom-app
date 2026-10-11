<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Ai\Agents\CommandCopilot;
use App\Data\Copilot\ProposedChangeSetData;
use App\Exceptions\AiUsageLimitExceededException;
use App\Models\User;
use App\Services\AiUsageService;

class CopilotService
{
    /**
     * Ask the copilot a question. Any changes it proposes are returned for review and are
     * never applied here.
     *
     * @return array{answer: string, changeSet: ProposedChangeSetData}
     *
     * @throws AiUsageLimitExceededException
     */
    public static function ask(string $prompt, User $actor): array
    {
        AiUsageService::ensureWithinLimit();

        $changeSet = new ProposedChangeSetData;

        $response = new CommandCopilot($actor, $changeSet)->prompt($prompt);

        return [
            'answer' => $response->text,
            'changeSet' => $changeSet,
        ];
    }
}
