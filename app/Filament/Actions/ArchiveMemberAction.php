<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use App\Data\Copilot\ProposedOperationData;
use App\Filament\Actions\Concerns\AppliesChangeSets;
use App\Models\Enums\CopilotOperationType;
use App\Models\Status;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Retire a member without losing their service history: their status changes through the same audited
 * change set as every other status record, and the profile and all of its records are kept.
 */
class ArchiveMemberAction extends Action
{
    use AppliesChangeSets;

    /**
     * Status names that usually mean someone has left, used to pre-select the status.
     */
    public const array LEAVING_STATUS_KEYWORDS = ['discharg', 'retir', 'separat', 'archiv', 'inactive', 'former', 'alumni'];

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Archive')
            ->icon(Heroicon::OutlinedArchiveBox)
            ->color('gray')
            ->modalIcon(Heroicon::OutlinedArchiveBox)
            ->modalHeading(fn (User $record): string => sprintf('Archive %s', $record->name))
            ->modalDescription('Their profile and full service history are kept. Only their status changes, and you can change it back at any time.')
            ->modalSubmitActionLabel('Archive Member')
            ->visible(fn (?User $record): bool => $record instanceof User && Gate::check('update', $record) && Status::query()->exists())
            ->fillForm(fn (): array => ['status_id' => static::suggestedStatusId()])
            ->schema([
                Select::make('status_id')
                    ->label('New Status')
                    ->helperText('The status that marks them as no longer active, such as Discharged or Retired.')
                    ->options(fn (): array => Status::query()->orderBy('order')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Textarea::make('text')
                    ->label('Note')
                    ->helperText('Optional. Saved with the status change in their history.')
                    ->rows(3),
            ])
            ->action(function (Action $action, User $record, array $data): void {
                $this->applyOperation($action, new ProposedOperationData(
                    type: CopilotOperationType::STATUS_RECORD,
                    userIds: [$record->getKey()],
                    targetId: (int) $data['status_id'],
                    text: filled($data['text'] ?? null) ? $data['text'] : null,
                    summary: sprintf('Archive %s as %s', $record->name, Status::query()->find($data['status_id'])?->name),
                ), 'profile action');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'archiveMember';
    }

    public static function suggestedStatusId(): ?int
    {
        return Status::query()
            ->get(['id', 'name'])
            ->first(fn (Status $status): bool => collect(ArchiveMemberAction::LEAVING_STATUS_KEYWORDS)
                ->contains(fn (string $keyword): bool => Str::contains(Str::lower($status->name), $keyword)))
            ?->getKey();
    }
}
