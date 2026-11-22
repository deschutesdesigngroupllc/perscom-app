<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * The standard way to pick members. It searches on the server instead of preloading everyone, and
 * labels each person with rank and unit so two members with the same name can be told apart.
 */
class MemberSelect extends Select
{
    public const int SEARCH_LIMIT = 50;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->searchable()
            ->searchPrompt('Search members by name or email')
            ->getSearchResultsUsing(fn (string $search): array => MemberSelect::searchMembers($search))
            ->getOptionLabelUsing(function (mixed $value): ?string {
                $user = User::query()->find($value);

                return $user instanceof User ? MemberSelect::memberLabel($user) : null;
            })
            ->getOptionLabelsUsing(fn (array $values): array => MemberSelect::labelsFor(User::query()->whereKey($values)->get()))
            ->getOptionLabelFromRecordUsing(fn (User $record): string => MemberSelect::memberLabel($record));
    }

    /**
     * @param  Collection<int, string|null>|null  $rankAbbreviations  rank abbreviations keyed by rank id, to label many members at once
     * @param  Collection<int, string|null>|null  $unitNames  unit names keyed by unit id
     */
    public static function memberLabel(User $user, ?Collection $rankAbbreviations = null, ?Collection $unitNames = null): string
    {
        $rank = $rankAbbreviations instanceof Collection ? $rankAbbreviations->get($user->rank_id) : $user->rank?->abbreviation;
        $unit = $unitNames instanceof Collection ? $unitNames->get($user->unit_id) : $user->unit?->name;

        $name = trim(sprintf('%s %s', $rank, $user->name));

        return filled($unit) ? sprintf('%s · %s', $name, $unit) : $name;
    }

    /**
     * @param  EloquentCollection<int, User>  $users
     * @return array<int, string>
     */
    public static function labelsFor(EloquentCollection $users): array
    {
        $rankAbbreviations = Rank::query()->whereKey($users->pluck('rank_id')->filter()->unique())->pluck('abbreviation', 'id');
        $unitNames = Unit::query()->whereKey($users->pluck('unit_id')->filter()->unique())->pluck('name', 'id');

        return $users
            ->mapWithKeys(fn (User $user): array => [$user->getKey() => MemberSelect::memberLabel($user, $rankAbbreviations, $unitNames)])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function searchMembers(string $search): array
    {
        return MemberSelect::labelsFor(User::query()
            ->where(fn (Builder $query) => $query->where('name', 'like', sprintf('%%%s%%', $search))->orWhere('email', 'like', sprintf('%%%s%%', $search)))
            ->orderBy('name')
            ->limit(MemberSelect::SEARCH_LIMIT)
            ->get());
    }
}
