<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Models\Rank;
use App\Models\Unit;
use App\Models\User;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
     * Build the member's label from rank, name, and unit.
     *
     * @param  Collection<int, string|null>|null  $rankAbbreviations  rank abbreviations keyed by rank id, to label many members at once
     * @param  Collection<int, string|null>|null  $unitNames  unit names keyed by unit id
     */
    public static function memberLabel(User $user, ?Collection $rankAbbreviations = null, ?Collection $unitNames = null): string
    {
        $rank = $rankAbbreviations instanceof Collection ? $rankAbbreviations->get($user->rank_id) : $user->rank?->abbreviation;
        $unit = $unitNames instanceof Collection ? $unitNames->get($user->unit_id) : $user->unit?->name;

        $name = Str::trim(sprintf('%s %s', $rank, $user->name));

        return filled($unit) ? sprintf('%s · %s', $name, $unit) : $name;
    }

    /**
     * Label each of the given members with rank and unit.
     *
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
     * Search members by name or email and label the matches.
     *
     * @return array<int, string>
     */
    public static function searchMembers(string $search): array
    {
        return MemberSelect::labelsFor(User::query()
            ->where(fn (Builder $query): Builder => $query
                ->where('name', 'like', sprintf('%%%s%%', $search))
                ->orWhere('email', 'like', sprintf('%%%s%%', $search)))
            ->orderBy('name')
            ->limit(MemberSelect::SEARCH_LIMIT)
            ->get());
    }

    /**
     * Fill the field from the page URL (for example ?user_id=4 or ?user_id[]=4&user_id[]=9) when a form
     * is opened from a member's Add Record menu or a bulk action on the members table.
     */
    public function prefillFromQuery(): static
    {
        return $this->default(function (): array|int|null {
            $ids = collect(Arr::wrap(request()->query($this->getName())))
                ->filter(fn (mixed $id): bool => is_numeric($id))
                ->map(fn (mixed $id): int => (int) $id)
                ->values();

            if ($ids->isEmpty()) {
                return null;
            }

            return $this->isMultiple() ? $ids->all() : $ids->first();
        });
    }
}
