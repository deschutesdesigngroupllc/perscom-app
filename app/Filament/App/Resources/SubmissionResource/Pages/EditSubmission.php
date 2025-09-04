<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Pages;

use App\Filament\App\Resources\SubmissionResource;
use App\Models\Submission;
use App\Traits\Filament\InteractsWithFields;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSubmission extends EditRecord
{
    use InteractsWithFields;

    protected static string $resource = SubmissionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        /** @var Submission $submission */
        $submission = $this->record;
        $submission->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ActionGroup::make([
                Action::make('mark-as-unread')
                    ->label('Mark As Unread')
                    ->visible(fn (Submission $record) => filled($record->read_at))
                    ->icon('heroicon-o-envelope-open')
                    ->action(function (Submission $record): void {
                        $record->markAsUnread();
                        $this->redirect(SubmissionResource::getUrl());
                    }),
            ]),
        ];
    }
}
