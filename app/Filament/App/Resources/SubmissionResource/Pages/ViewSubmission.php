<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Pages;

use App\Filament\App\Resources\SubmissionResource;
use App\Models\Submission;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubmission extends ViewRecord
{
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
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
