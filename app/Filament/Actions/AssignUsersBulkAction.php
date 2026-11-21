<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Unit;
use App\Models\User;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class AssignUsersBulkAction extends BulkAction
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Assign')
            ->icon(Heroicon::OutlinedRectangleGroup)
            ->modalHeading('Add Assignment Record')
            ->modalDescription('Create an assignment record for each selected member. Each member is notified as usual.')
            ->modalSubmitActionLabel('Create Assignment Records')
            ->visible(fn (): bool => Gate::check('create', AssignmentRecord::class))
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
            Select::make('unit_id')
                ->label('Unit')
                ->helperText('The unit to assign them to.')
                ->options(fn (): array => Unit::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            Select::make('position_id')
                ->label('Position')
                ->helperText('The optional position for the assignment.')
                ->options(fn (): array => Position::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable(),
            Select::make('specialty_id')
                ->label('Specialty')
                ->helperText('The optional specialty for the assignment.')
                ->options(fn (): array => Specialty::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable(),
            Select::make('type')
                ->label('Type')
                ->helperText('Whether this is a primary or secondary assignment.')
                ->options(AssignmentRecordType::class)
                ->default(AssignmentRecordType::PRIMARY)
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
        $type = $data['type'] instanceof AssignmentRecordType ? $data['type'] : AssignmentRecordType::from((string) $data['type']);

        return new ProposedOperationData(
            type: CopilotOperationType::ASSIGNMENT_RECORD,
            userIds: $users->map(fn (User $user): int => $user->getKey())->values()->all(),
            targetId: (int) $data['unit_id'],
            text: $data['text'] ?? null,
            attributes: array_filter([
                'type' => $type->value,
                'position_id' => $data['position_id'] ?? null,
                'specialty_id' => $data['specialty_id'] ?? null,
            ], filled(...)),
            summary: sprintf('Assign %d member(s) to %s', $users->count(), Unit::query()->find($data['unit_id'])?->name),
        );
    }

    public static function getDefaultName(): ?string
    {
        return 'assign';
    }
}
