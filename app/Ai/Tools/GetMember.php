<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\DescribesMembers;
use App\Models\AwardRecord;
use App\Models\QualificationRecord;
use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetMember implements Tool
{
    use DescribesMembers;

    public function __construct(protected User $actor) {}

    public function description(): Stringable|string
    {
        return 'Get one member\'s profile, including their recent rank records, awards and qualifications.';
    }

    public function handle(Request $request): Stringable|string
    {
        $user = SearchMembers::query([])
            ->whereKey($request->integer('id'))
            ->first();

        if (! $user instanceof User) {
            return 'No member exists with that id.';
        }

        if (! $this->actor->can('view', $user)) {
            return 'You are not allowed to view this member.';
        }

        return (string) json_encode([
            ...$this->describeMember($user),
            'rank_records' => $user->rank_records()->with('rank')->limit(10)->get()->map(fn (RankRecord $record): array => [
                'rank' => $record->rank?->name,
                'type' => $record->type?->getLabel(),
                'date' => $record->created_at?->toDateString(),
            ])->all(),
            'awards' => $user->award_records()->with('award')->latest()->limit(25)->get()->map(fn (AwardRecord $record): array => [
                'award' => $record->award?->name,
                'date' => $record->created_at?->toDateString(),
            ])->all(),
            'qualifications' => $user->qualification_records()->with('qualification')->limit(25)->get()->map(fn (QualificationRecord $record): array => [
                'qualification' => $record->qualification?->name,
                'date' => $record->created_at?->toDateString(),
            ])->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The member id.')->required(),
        ];
    }
}
