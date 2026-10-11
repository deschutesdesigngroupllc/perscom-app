<?php

declare(strict_types=1);

namespace App\Ai\Tools\Proposals;

use App\Data\Copilot\ProposedOperationData;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\NotificationChannel;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stringable;

class ProposeMessage extends ProposalTool
{
    /**
     * @var array<int, NotificationChannel>
     */
    public const array CHANNELS = [
        NotificationChannel::DATABASE,
        NotificationChannel::MAIL,
        NotificationChannel::SMS,
        NotificationChannel::DISCORD_PRIVATE,
    ];

    public function description(): Stringable|string
    {
        return 'Propose sending a message to one or more members. This only queues the message for the user to review.';
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            ...$this->memberSchema($schema),
            'message' => $schema->string()->description('The message body.')->required(),
            'channels' => $schema->array()->items($schema->string()->enum(collect(static::CHANNELS)->map->value->all()))->description('Delivery channels. Defaults to database and mail.'),
        ];
    }

    protected function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:65535'],
            'channels' => ['nullable', 'array'],
            'channels.*' => [Rule::in(collect(static::CHANNELS)->map->value->all())],
        ];
    }

    protected function propose(array $validated): ProposedOperationData
    {
        return new ProposedOperationData(
            type: CopilotOperationType::MESSAGE,
            userIds: $validated['user_ids'],
            text: $validated['message'],
            attributes: ['channels' => filled($validated['channels'] ?? null)
                ? array_values($validated['channels'])
                : [NotificationChannel::DATABASE->value, NotificationChannel::MAIL->value]],
            summary: sprintf('Message %s: "%s"', $this->describeMembers($validated['user_ids']), Str::limit($validated['message'], 80)),
        );
    }
}
