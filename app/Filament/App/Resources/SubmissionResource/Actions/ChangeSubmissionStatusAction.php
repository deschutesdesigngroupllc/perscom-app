<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Actions;

use App\Models\Status;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;

class ChangeSubmissionStatusAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Change Status')
            ->icon(Heroicon::OutlinedScale)
            ->modalHeading('Change Status')
            ->modalDescription("The new status is added to this submission's history.")
            ->modalSubmitActionLabel('Update Status')
            ->visible(fn (Submission $record): bool => Gate::check('update', $record) && Status::query()->exists())
            ->fillForm(fn (Submission $record): array => ['status_id' => $record->status?->getKey()])
            ->schema([
                Select::make('status_id')
                    ->label('Status')
                    ->options(fn (): array => Status::query()->orderBy('order')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Textarea::make('text')
                    ->label('Note')
                    ->helperText('Optional. Saved with the status in its history.')
                    ->rows(3),
            ])
            ->successNotificationTitle('Status Updated')
            ->action(function (Action $action, Submission $record, array $data): void {
                $record->statuses()->attach((int) $data['status_id'], ['text' => filled($data['text'] ?? null) ? $data['text'] : null]);

                $action->success();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'changeStatus';
    }
}
