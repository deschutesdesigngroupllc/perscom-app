<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Pages;

use App\Filament\App\Resources\SubmissionResource;
use App\Filament\App\Resources\SubmissionResource\Actions\ChangeSubmissionStatusAction;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    public function booted(): void
    {
        /** @var Submission $submission */
        $submission = $this->record;
        $submission->markAsRead();
    }

    /**
     * The oldest unread submission other than this one, so reviewers can work through the queue in order.
     */
    public function nextUnreadSubmission(): ?Submission
    {
        return Submission::query()
            ->unread()
            ->whereKeyNot($this->getRecord()->getKey())
            ->oldest()
            ->first();
    }

    /**
     * @return array<int, Action|ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            ChangeSubmissionStatusAction::make(),
            Action::make('nextUnread')
                ->label('Next Unread')
                ->icon(Heroicon::OutlinedArrowRight)
                ->iconPosition(IconPosition::After)
                ->color('gray')
                ->visible(fn (): bool => $this->nextUnreadSubmission() instanceof Submission)
                ->url(fn (): ?string => ($next = $this->nextUnreadSubmission()) instanceof Submission ? SubmissionResource::getUrl('view', ['record' => $next]) : null),
            EditAction::make()
                ->color('gray'),
            ActionGroup::make([
                DeleteAction::make(),
            ])
                ->tooltip('More')
                ->color('gray')
                ->button()
                ->label('More')
                ->icon(Heroicon::OutlinedEllipsisHorizontal),
        ];
    }
}
