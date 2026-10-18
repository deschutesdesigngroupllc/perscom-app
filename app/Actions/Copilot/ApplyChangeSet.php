<?php

declare(strict_types=1);

namespace App\Actions\Copilot;

use App\Data\Copilot\ProposedChangeSetData;
use App\Data\Copilot\ProposedOperationData;
use App\Models\AssignmentRecord;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\NotificationChannel;
use App\Models\Enums\RankRecordType;
use App\Models\Message;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Throwable;

class ApplyChangeSet
{
    /**
     * Authorize and apply every operation in the change set as the given actor. Nothing is
     * written unless every operation is authorized and valid. Records are created through
     * Eloquent so the usual observers send notifications, webhooks and automations.
     *
     * @return array{created: int, operations: array<int, array{type: string, summary: string, created: int}>}
     *
     * @throws AuthorizationException
     * @throws ValidationException
     * @throws Throwable
     */
    public static function handle(ProposedChangeSetData $changeSet, User $actor, string $source = 'copilot'): array
    {
        $operations = collect($changeSet->operations);

        $users = User::query()
            ->whereKey($operations->flatMap(fn (ProposedOperationData $operation): array => $operation->userIds)->unique()->all())
            ->get()
            ->keyBy('id');

        $operations->each(fn (ProposedOperationData $operation) => static::validate($operation, $users));
        $operations->each(fn (ProposedOperationData $operation) => static::authorize($operation, $actor, $users));

        $results = DB::transaction(fn (): array => $operations
            ->map(fn (ProposedOperationData $operation): array => [
                'type' => $operation->type->value,
                'summary' => $operation->summary,
                'created' => static::apply($operation, $actor, $users),
            ])
            ->all());

        $created = collect($results)->sum('created');

        activity($source)
            ->causedBy($actor)
            ->withProperties(['source' => $source, 'operations' => $changeSet->summaries()])
            ->log(sprintf('Applied %d change(s) via the %s.', $created, $source));

        return [
            'created' => $created,
            'operations' => $results,
        ];
    }

    /**
     * @param  Collection<int, User>  $users
     *
     * @throws ValidationException
     */
    protected static function validate(ProposedOperationData $operation, Collection $users): void
    {
        if (blank($operation->userIds)) {
            throw ValidationException::withMessages(['operations' => sprintf('The operation "%s" has no members.', $operation->summary)]);
        }

        if (collect($operation->userIds)->contains(fn (int $id): bool => ! $users->has($id))) {
            throw ValidationException::withMessages(['operations' => sprintf('The operation "%s" refers to a member that does not exist.', $operation->summary)]);
        }

        $targetModel = match ($operation->type) {
            CopilotOperationType::RANK_RECORD => Rank::class,
            CopilotOperationType::ASSIGNMENT_RECORD => Unit::class,
            CopilotOperationType::AWARD_RECORD => Award::class,
            CopilotOperationType::QUALIFICATION_RECORD => Qualification::class,
            CopilotOperationType::STATUS_RECORD => Status::class,
            CopilotOperationType::MESSAGE => null,
        };

        if ($targetModel !== null && ($operation->targetId === null || ! $targetModel::query()->whereKey($operation->targetId)->exists())) {
            throw ValidationException::withMessages(['operations' => sprintf('The operation "%s" refers to a %s that does not exist.', $operation->summary, class_basename($targetModel))]);
        }

        if ($operation->type === CopilotOperationType::MESSAGE && blank($operation->text)) {
            throw ValidationException::withMessages(['operations' => 'A message cannot be empty.']);
        }
    }

    /**
     * @param  Collection<int, User>  $users
     *
     * @throws AuthorizationException
     */
    protected static function authorize(ProposedOperationData $operation, User $actor, Collection $users): void
    {
        $gate = Gate::forUser($actor);
        $ability = $operation->type->getAuthorizationAbility();

        if ($operation->type === CopilotOperationType::STATUS_RECORD) {
            collect($operation->userIds)->each(fn (int $id) => $gate->authorize($ability, $users->get($id)));

            return;
        }

        $gate->authorize($ability, $operation->type->getAuthorizableModel());
    }

    /**
     * @param  Collection<int, User>  $users
     */
    protected static function apply(ProposedOperationData $operation, User $actor, Collection $users): int
    {
        if ($operation->type === CopilotOperationType::MESSAGE) {
            Message::create([
                'message' => $operation->text,
                'recipients' => collect($operation->userIds)->values()->all(),
                'channels' => $operation->attributes['channels'] ?? [NotificationChannel::DATABASE->value, NotificationChannel::MAIL->value],
            ]);

            return 1;
        }

        return collect($operation->userIds)
            ->each(fn (int $id) => static::applyToUser($operation, $actor, $users->get($id)))
            ->count();
    }

    protected static function applyToUser(ProposedOperationData $operation, User $actor, User $user): void
    {
        match ($operation->type) {
            CopilotOperationType::RANK_RECORD => RankRecord::create([
                'user_id' => $user->getKey(),
                'rank_id' => $operation->targetId,
                'author_id' => $actor->getKey(),
                'type' => RankRecordType::tryFrom((int) ($operation->attributes['type'] ?? RankRecordType::PROMOTION->value)) ?? RankRecordType::PROMOTION,
                'text' => $operation->text,
            ]),
            CopilotOperationType::ASSIGNMENT_RECORD => AssignmentRecord::create([
                'user_id' => $user->getKey(),
                'unit_id' => $operation->targetId,
                'position_id' => $operation->attributes['position_id'] ?? null,
                'specialty_id' => $operation->attributes['specialty_id'] ?? null,
                'author_id' => $actor->getKey(),
                'type' => AssignmentRecordType::tryFrom((string) ($operation->attributes['type'] ?? '')) ?? AssignmentRecordType::PRIMARY,
                'text' => $operation->text,
            ]),
            CopilotOperationType::AWARD_RECORD => AwardRecord::create([
                'user_id' => $user->getKey(),
                'award_id' => $operation->targetId,
                'author_id' => $actor->getKey(),
                'text' => $operation->text,
            ]),
            CopilotOperationType::QUALIFICATION_RECORD => QualificationRecord::create([
                'user_id' => $user->getKey(),
                'qualification_id' => $operation->targetId,
                'author_id' => $actor->getKey(),
                'text' => $operation->text,
            ]),
            CopilotOperationType::STATUS_RECORD => static::changeStatus($user, (int) $operation->targetId, $operation->text),
            CopilotOperationType::MESSAGE => null,
        };
    }

    protected static function changeStatus(User $user, int $statusId, ?string $text): void
    {
        $user->statuses()->attach($statusId, ['text' => $text]);

        $user->forceFill(['status_id' => $statusId])->save();
    }
}
