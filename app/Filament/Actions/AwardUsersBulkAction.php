<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\Award;
use App\Models\AwardRecord;
use App\Models\Enums\CopilotOperationType;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class AwardUsersBulkAction extends BulkAction
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Award')
            ->icon(Heroicon::OutlinedTrophy)
            ->modalHeading('Add Award Record')
            ->modalDescription('Create an award record for each selected member. Each member is notified as usual.')
            ->modalSubmitActionLabel('Create Award Records')
            ->visible(fn (): bool => Gate::check('create', AwardRecord::class))
            ->deselectRecordsAfterCompletion()
            ->schema(static::formSchema())
            ->action(fn (BulkAction $action, Collection $records, array $data) => $this->applyOperation($action, static::operationFor($records, $data)));
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function formSchema(): array
    {
        return [
            Select::make('award_id')
                ->label('Award')
                ->helperText('The award to give.')
                ->options(fn (): array => Award::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            RichEditor::make('text')
                ->label('Text')
                ->helperText('Optional citation included with each record.'),
        ];
    }

    /**
     * @param  Collection<int, User>  $users
     * @param  array<string, mixed>  $data
     */
    public static function operationFor(Collection $users, array $data): ProposedOperationData
    {
        return new ProposedOperationData(
            type: CopilotOperationType::AWARD_RECORD,
            userIds: $users->map(fn (User $user): int => $user->getKey())->values()->all(),
            targetId: (int) $data['award_id'],
            text: $data['text'] ?? null,
            summary: sprintf('Award %s to %d member(s)', Award::query()->find($data['award_id'])?->name, $users->count()),
        );
    }

    public static function getDefaultName(): ?string
    {
        return 'award';
    }
}
