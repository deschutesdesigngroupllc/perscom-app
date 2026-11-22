<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\Enums\CopilotOperationType;
use App\Models\Qualification;
use App\Models\QualificationRecord;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

/**
 * Record a qualification for one member through the same audited change set as Promote and Award.
 */
class QualifyMemberAction extends Action
{
    use AppliesChangeSets;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Qualify')
            ->icon(Heroicon::OutlinedStar)
            ->modalHeading(fn (?Model $record, Component $livewire): string => sprintf('Qualify %s', PromoteMemberAction::resolveMember($record, $livewire)?->name))
            ->modalDescription('Adds a qualification record to their profile. They are notified as usual.')
            ->modalSubmitActionLabel('Add Qualification')
            ->visible(fn (): bool => Gate::check('create', QualificationRecord::class))
            ->schema([
                Select::make('qualification_id')
                    ->label('Qualification')
                    ->helperText('The qualification they earned.')
                    ->options(fn (): array => Qualification::query()->orderBy('order')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                RichEditor::make('text')
                    ->label('Text')
                    ->helperText('Optional text included with the record.'),
            ])
            ->action(function (Action $action, ?Model $record, Component $livewire, array $data): void {
                $member = PromoteMemberAction::resolveMember($record, $livewire);

                if (! $member instanceof User) {
                    return;
                }

                $this->applyOperation($action, new ProposedOperationData(
                    type: CopilotOperationType::QUALIFICATION_RECORD,
                    userIds: [$member->getKey()],
                    targetId: (int) $data['qualification_id'],
                    text: $data['text'] ?? null,
                    summary: sprintf('Qualify %s in %s', $member->name, Qualification::query()->find($data['qualification_id'])?->name),
                ), 'profile action');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'qualifyMember';
    }
}
