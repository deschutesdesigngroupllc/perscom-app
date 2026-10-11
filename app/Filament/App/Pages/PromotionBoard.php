<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Data\Promotions\PromotionEvaluationData;
use App\Data\Promotions\RequirementResultData;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\User;
use App\Services\PromotionEligibilityService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use UnitEnum;

class PromotionBoard extends Page implements HasTable
{
    use InteractsWithTable;

    #[Url]
    public string $activeTab = 'eligible';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowTrendingUp;

    protected static string|UnitEnum|null $navigationGroup = 'Personnel';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Promotion Board';

    protected ?string $subheading = 'Members who meet the requirements for their next rank, and what the rest still need.';

    /**
     * @var Collection<int, PromotionEvaluationData>|null
     */
    protected ?Collection $evaluations = null;

    public static function canAccess(): bool
    {
        return Auth::user()?->can('viewAny', RankRecord::class) ?? false;
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
        $this->deselectAllTableRecords();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->key('promotionTabs')
                    ->livewireProperty('activeTab')
                    ->contained(false)
                    ->tabs([
                        'eligible' => Tab::make('Eligible')
                            ->icon(Heroicon::OutlinedCheckBadge)
                            ->badge(fn (): int => $this->evaluations()->where('eligible', true)->count()),
                        'not_yet_eligible' => Tab::make('Not Yet Eligible')
                            ->icon(Heroicon::OutlinedClock)
                            ->badge(fn (): int => $this->evaluations()->where('eligible', false)->count()),
                    ]),
                EmbeddedTable::make(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => User::query()
                ->whereKey($this->evaluations()
                    ->filter(fn (PromotionEvaluationData $evaluation): bool => $evaluation->eligible === ($this->activeTab === 'eligible'))
                    ->keys()
                    ->all()))
            ->emptyStateIcon(Heroicon::OutlinedArrowTrendingUp)
            ->emptyStateHeading(fn (): string => $this->activeTab === 'eligible' ? 'No Members Are Eligible' : 'Everyone Eligible Is Listed')
            ->emptyStateDescription('Configure promotion requirements on each rank to see members here.')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Unit')
                    ->placeholder('No Unit')
                    ->sortable(),
                TextColumn::make('rank.name')
                    ->label('Current Rank')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('next_rank')
                    ->label('Next Rank')
                    ->badge()
                    ->color('success')
                    ->state(fn (User $record): ?string => $this->evaluationFor($record)?->nextRank?->name),
                TextColumn::make('requirements')
                    ->label(fn (): string => $this->activeTab === 'eligible' ? 'Requirements Met' : 'Still Needed')
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->wrap()
                    ->state(fn (User $record): array => $this->requirementsFor($record)),
            ])
            ->filters([
                SelectFilter::make('unit')
                    ->relationship('unit', 'name')
                    ->preload()
                    ->searchable(),
            ])
            ->recordActions([
                Action::make('promote')
                    ->label('Promote')
                    ->icon(Heroicon::OutlinedArrowUpCircle)
                    ->color('success')
                    ->visible(fn (): bool => $this->activeTab === 'eligible' && $this->canPromote())
                    ->modalHeading(fn (User $record): string => sprintf('Promote %s', $record->name))
                    ->modalDescription(fn (User $record): string => sprintf('This will create a promotion rank record to %s.', $this->evaluationFor($record)?->nextRank?->name))
                    ->schema([
                        Textarea::make('text')
                            ->label('Promotion Text')
                            ->helperText('An optional note included on the rank record.')
                            ->nullable(),
                    ])
                    ->action(function (User $record, array $data): void {
                        $count = $this->promote(new EloquentCollection([$record]), $data['text'] ?? null);

                        $this->notifyPromoted($count);
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('promoteSelected')
                    ->label('Promote Selected')
                    ->icon(Heroicon::OutlinedArrowUpCircle)
                    ->color('success')
                    ->visible(fn (): bool => $this->activeTab === 'eligible' && $this->canPromote())
                    ->modalHeading('Promote Selected Members')
                    ->modalDescription('Each selected member will receive a promotion rank record to their next rank.')
                    ->schema([
                        Textarea::make('text')
                            ->label('Promotion Text')
                            ->helperText('An optional note included on each rank record.')
                            ->nullable(),
                    ])
                    ->deselectRecordsAfterCompletion()
                    ->action(function (EloquentCollection $records, array $data): void {
                        $count = $this->promote($records, $data['text'] ?? null);

                        $this->notifyPromoted($count);
                    }),
            ]);
    }

    /**
     * Promote each member who is still eligible to their next rank. Ineligible members are skipped.
     *
     * @param  EloquentCollection<int, User>  $users
     */
    public function promote(EloquentCollection $users, ?string $text = null): int
    {
        abort_unless($this->canPromote(), 403);

        $promotable = PromotionEligibilityService::evaluateMany($users)
            ->filter(fn (PromotionEvaluationData $evaluation): bool => $evaluation->eligible && $evaluation->nextRank instanceof Rank);

        DB::transaction(fn () => $promotable->each(fn (PromotionEvaluationData $evaluation): RankRecord => RankRecord::create([
            'user_id' => $evaluation->user->getKey(),
            'rank_id' => $evaluation->nextRank->getKey(),
            'author_id' => Auth::id(),
            'type' => RankRecordType::PROMOTION,
            'text' => filled($text) ? $text : sprintf('Promoted to %s.', $evaluation->nextRank->name),
        ])));

        $this->evaluations = null;

        return $promotable->count();
    }

    /**
     * @return Collection<int, PromotionEvaluationData>
     */
    protected function evaluations(): Collection
    {
        return $this->evaluations ??= PromotionEligibilityService::evaluateAll()
            ->filter(fn (PromotionEvaluationData $evaluation): bool => $evaluation->nextRank instanceof Rank);
    }

    protected function evaluationFor(User $user): ?PromotionEvaluationData
    {
        return $this->evaluations()->get($user->getKey());
    }

    /**
     * @return array<int, string>
     */
    protected function requirementsFor(User $user): array
    {
        $evaluation = $this->evaluationFor($user);

        if (! $evaluation instanceof PromotionEvaluationData) {
            return [];
        }

        if (filled($evaluation->reason)) {
            return [$evaluation->reason];
        }

        $results = $this->activeTab === 'eligible' ? $evaluation->metRequirements() : $evaluation->unmetRequirements();

        return collect($results)
            ->map(fn (RequirementResultData $result): string => sprintf('%s: %s', $result->label, $result->detail))
            ->all();
    }

    protected function canPromote(): bool
    {
        return Auth::user()?->can('create', RankRecord::class) ?? false;
    }

    protected function notifyPromoted(int $count): void
    {
        Notification::make()
            ->title($count === 1 ? '1 member has been promoted.' : sprintf('%d members have been promoted.', $count))
            ->success()
            ->send();
    }
}
