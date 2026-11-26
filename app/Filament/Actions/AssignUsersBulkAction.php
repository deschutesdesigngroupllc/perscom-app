<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\AssignmentRecord;
use App\Models\Enums\AssignmentRecordType;
use App\Models\Enums\CopilotOperationType;
use App\Models\Enums\RosterMode;
use App\Models\Position;
use App\Models\Specialty;
use App\Models\Unit;
use App\Models\UnitSlot;
use App\Models\User;
use App\Settings\DashboardSettings;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

use function sprintf;

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
            ->action(fn (BulkAction $action, Collection $records, array $data): null => $this->applyOperation(
                $action,
                static::operationFor($records, $data),
            ));
    }

    /**
     * Build the form that collects the assignment details.
     *
     * @return array<int, \Filament\Schemas\Components\Component|\Filament\Forms\Components\Field>
     */
    public static function formSchema(): array
    {
        return [
            Select::make('unit_slot_id')
                ->label('Slot')
                ->helperText('The slot to assign them to. They also take the unit it belongs to and any position or specialty set on the slot.')
                ->options(fn (): array => Unit::ordered()->with('slots')->get()
                    ->mapWithKeys(fn (Unit $unit): array => [$unit->name => $unit->slots->pluck('name', 'pivot.id')->all()])
                    ->filter()
                    ->all())
                ->searchable()
                ->visible(fn (): bool => static::usesManualRoster())
                ->required(fn (): bool => static::usesManualRoster()),
            Select::make('unit_id')
                ->label('Unit')
                ->helperText('The unit to assign them to.')
                ->options(fn (): array => Unit::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable()
                ->hidden(fn (): bool => static::usesManualRoster())
                ->required(fn (): bool => ! static::usesManualRoster()),
            Select::make('position_id')
                ->label('Position')
                ->helperText('The optional position for the assignment.')
                ->options(fn (): array => Position::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable()
                ->hidden(fn (): bool => static::usesManualRoster()),
            Select::make('specialty_id')
                ->label('Specialty')
                ->helperText('The optional specialty for the assignment.')
                ->options(fn (): array => Specialty::query()->orderBy('order')->pluck('name', 'id')->all())
                ->searchable()
                ->hidden(fn (): bool => static::usesManualRoster()),
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
     * Build the assignment operation for the selected members.
     *
     * @param  Collection<int, User>  $users
     * @param  array<string, mixed>  $data
     */
    public static function operationFor(Collection $users, array $data): ProposedOperationData
    {
        $type = $data['type'] instanceof AssignmentRecordType ? $data['type'] : AssignmentRecordType::from((string) $data['type']);
        $unitSlot = filled($data['unit_slot_id'] ?? null) ? UnitSlot::query()->find($data['unit_slot_id']) : null;
        $unitId = $unitSlot instanceof UnitSlot ? $unitSlot->unit_id : ($data['unit_id'] ?? null);

        return new ProposedOperationData(
            type: CopilotOperationType::ASSIGNMENT_RECORD,
            userIds: $users->map(fn (User $user): int => $user->getKey())->values()->all(),
            targetId: (int) $unitId,
            text: $data['text'] ?? null,
            attributes: collect([
                'type' => $type->value,
                'unit_slot_id' => $unitSlot?->getKey(),
                'position_id' => $unitSlot instanceof UnitSlot ? null : ($data['position_id'] ?? null),
                'specialty_id' => $unitSlot instanceof UnitSlot ? null : ($data['specialty_id'] ?? null),
            ])->filter(filled(...))->all(),
            summary: sprintf('Assign %d member(s) to %s', $users->count(), Unit::query()->find($unitId)?->name),
        );
    }

    /**
     * In manual roster mode members are placed into slots rather than straight into units.
     */
    public static function usesManualRoster(): bool
    {
        return resolve(DashboardSettings::class)->roster_mode === RosterMode::MANUAL;
    }

    public static function getDefaultName(): ?string
    {
        return 'assign';
    }
}
