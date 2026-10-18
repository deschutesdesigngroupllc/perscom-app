<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\RankRecordType;
use App\Models\Rank;
use App\Models\RankRecord;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class PromoteUsersBulkAction extends BulkAction
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Promote')
            ->icon(Heroicon::OutlinedChevronDoubleUp)
            ->modalHeading('Add Rank Record')
            ->modalDescription('Create a rank record for each selected user. Each user is notified as usual.')
            ->modalSubmitActionLabel('Create Rank Records')
            ->visible(fn (): bool => Gate::check('create', RankRecord::class))
            ->deselectRecordsAfterCompletion()
            ->schema([
                Select::make('rank_id')
                    ->label('Rank')
                    ->helperText('The rank to record for each selected user.')
                    ->options(fn (): array => Rank::query()->orderBy('order')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->helperText('The type of rank record.')
                    ->options(RankRecordType::class)
                    ->default(RankRecordType::PROMOTION)
                    ->required(),
                RichEditor::make('text')
                    ->label('Text')
                    ->helperText('Optional text included with each record.'),
            ])
            ->action(function (BulkAction $action, Collection $records, array $data): void {
                $type = $data['type'] instanceof RankRecordType ? $data['type'] : RankRecordType::from((int) $data['type']);

                $this->applyOperation($action, new ProposedOperationData(
                    type: CopilotOperationType::RANK_RECORD,
                    userIds: $records->map(fn (User $user): int => $user->getKey())->values()->all(),
                    targetId: (int) $data['rank_id'],
                    text: $data['text'] ?? null,
                    attributes: ['type' => $type->value],
                    summary: sprintf('%s of %d user(s) to %s', $type->getLabel(), $records->count(), Rank::query()->find($data['rank_id'])?->name),
                ));
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'promote';
    }
}
