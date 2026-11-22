<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\Enums\CopilotOperationType;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class QualifyUsersBulkAction extends BulkAction
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Qualify')
            ->icon(Heroicon::OutlinedStar)
            ->modalHeading('Add Qualification Record')
            ->modalDescription('Create a qualification record for each selected member. Each member is notified as usual.')
            ->modalSubmitActionLabel('Create Qualification Records')
            ->visible(fn (): bool => Gate::check('create', QualificationRecord::class))
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
            Select::make('qualification_id')
                ->label('Qualification')
                ->helperText('The qualification they earned.')
                ->options(fn (): array => Qualification::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            RichEditor::make('text')
                ->label('Text')
                ->helperText('Optional text included with each record.'),
        ];
    }

    /**
     * @param  Collection<int, User>  $users
     * @param  array<string, mixed>  $data
     */
    public static function operationFor(Collection $users, array $data): ProposedOperationData
    {
        return new ProposedOperationData(
            type: CopilotOperationType::QUALIFICATION_RECORD,
            userIds: $users->map(fn (User $user): int => $user->getKey())->values()->all(),
            targetId: (int) $data['qualification_id'],
            text: $data['text'] ?? null,
            summary: sprintf('Qualify %d member(s) in %s', $users->count(), Qualification::query()->find($data['qualification_id'])?->name),
        );
    }

    public static function getDefaultName(): ?string
    {
        return 'qualify';
    }
}
