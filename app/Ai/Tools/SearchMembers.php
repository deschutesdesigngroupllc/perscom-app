<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Ai\Tools\Concerns\DescribesMembers;
use App\Models\Enums\EventRegistrationStatus;
use App\Models\RankRecord;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchMembers implements Tool
{
    use DescribesMembers;

    public const int LIMIT = 50;

    /**
     * @var array<int, string>
     */
    public const array RELATIONS = ['rank', 'unit', 'position', 'specialty', 'status'];

    public function __construct(protected User $actor) {}

    /**
     * Build the member query for the given filters.
     *
     * @param  array<string, mixed>  $filters
     * @return Builder<User>
     */
    public static function query(array $filters): Builder
    {
        $filters = collect($filters)->filter(fn (mixed $value): bool => filled($value));

        $lastRankRecordAt = RankRecord::query()
            ->withoutGlobalScopes()
            ->selectRaw('MAX(created_at)')
            ->whereColumn('records_ranks.user_id', 'users.id');

        return User::query()
            ->with(static::RELATIONS)
            ->select('users.*')
            ->selectSub($lastRankRecordAt, 'last_rank_record_at')
            ->when($filters->get('name'), fn (Builder $query, string $name) => $query->where('name', 'like', '%'.$name.'%'))
            ->when($filters->get('unit_id'), fn (Builder $query, int $id) => $query->where('unit_id', $id))
            ->when($filters->get('rank_id'), fn (Builder $query, int $id) => $query->where('rank_id', $id))
            ->when($filters->get('position_id'), fn (Builder $query, int $id) => $query->where('position_id', $id))
            ->when($filters->get('specialty_id'), fn (Builder $query, int $id) => $query->where('specialty_id', $id))
            ->when($filters->get('status_id'), fn (Builder $query, int $id) => $query->where('status_id', $id))
            ->when($filters->get('min_days_in_grade'), fn (Builder $query, int $days) => $query->where(clone $lastRankRecordAt, '<=', now()->subDays($days)))
            ->when($filters->get('max_days_in_grade'), fn (Builder $query, int $days) => $query->where(clone $lastRankRecordAt, '>=', now()->subDays($days)))
            ->when($filters->get('has_qualification_id'), fn (Builder $query, int $id) => $query->whereHas('qualification_records', fn (Builder $query) => $query->where('qualification_id', $id)))
            ->when($filters->get('missing_qualification_id'), fn (Builder $query, int $id) => $query->whereDoesntHave('qualification_records', fn (Builder $query) => $query->where('qualification_id', $id)))
            ->when($filters->get('last_seen_before'), fn (Builder $query, string $date) => $query->where(fn (Builder $query) => $query->whereNull('last_seen_at')->orWhere('last_seen_at', '<', $date)))
            ->when($filters->get('last_seen_after'), fn (Builder $query, string $date) => $query->where('last_seen_at', '>', $date))
            ->when($filters->has('min_events_attended'), fn (Builder $query) => $query->whereHas(
                'events',
                fn (Builder $query) => $query
                    ->where('events_registrations.status', EventRegistrationStatus::ATTENDED->value)
                    ->where('events.starts', '>=', now()->subDays((int) $filters->get('attended_within_days', 30))),
                '>=',
                (int) $filters->get('min_events_attended'),
            ))
            ->orderBy('name');
    }

    public function description(): Stringable|string
    {
        return 'Search the organization\'s members. All filters are optional and combined with AND. Returns at most '.static::LIMIT.' members with their id, rank, unit, position, status, days in grade and when they were last seen.';
    }

    public function handle(Request $request): Stringable|string
    {
        if (! $this->actor->can('viewAny', User::class)) {
            return 'You are not allowed to view members.';
        }

        $members = static::query($request->all())
            ->limit(static::LIMIT)
            ->get()
            ->map(fn (User $user): array => $this->describeMember($user));

        return (string) json_encode([
            'count' => $members->count(),
            'members' => $members->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->description('Part of the member\'s name.'),
            'unit_id' => $schema->integer()->description('Only members assigned to this unit.'),
            'rank_id' => $schema->integer()->description('Only members holding this rank.'),
            'position_id' => $schema->integer()->description('Only members in this position.'),
            'specialty_id' => $schema->integer()->description('Only members with this specialty.'),
            'status_id' => $schema->integer()->description('Only members with this status.'),
            'min_days_in_grade' => $schema->integer()->description('Only members whose latest rank record is at least this many days old.'),
            'max_days_in_grade' => $schema->integer()->description('Only members whose latest rank record is at most this many days old.'),
            'has_qualification_id' => $schema->integer()->description('Only members who hold this qualification.'),
            'missing_qualification_id' => $schema->integer()->description('Only members who do not hold this qualification.'),
            'last_seen_before' => $schema->string()->description('Only members last seen before this date (YYYY-MM-DD), including members never seen.'),
            'last_seen_after' => $schema->string()->description('Only members last seen after this date (YYYY-MM-DD).'),
            'min_events_attended' => $schema->integer()->description('Only members who attended at least this many events within attended_within_days.'),
            'attended_within_days' => $schema->integer()->description('The look-back window in days for min_events_attended. Defaults to 30.'),
        ];
    }
}
