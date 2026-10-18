<?php

declare(strict_types=1);

namespace App\Livewire\Filament\App;

use App\Actions\Copilot\ApplyChangeSet;
use App\Data\Copilot\ProposedChangeSetData;
use App\Exceptions\AiUsageLimitExceededException;
use App\Models\User;
use App\Services\Ai\CopilotService;
use App\Services\AiUsageService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Throwable;

class Copilot extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public ?string $answer = null;

    /**
     * The proposed change set, kept as an array so it survives Livewire requests.
     *
     * @var array<string, mixed>
     */
    public array $changeSet = [];

    public function render(): View
    {
        return view('livewire.filament.app.copilot');
    }

    public function isAvailable(): bool
    {
        return Auth::user() instanceof User && AiUsageService::isAvailable();
    }

    public function askAction(): Action
    {
        return Action::make('ask')
            ->label('Ask PERSCOM')
            ->icon(Heroicon::OutlinedSparkles)
            ->color('gray')
            ->outlined()
            ->size('sm')
            ->visible(fn (): bool => $this->isAvailable())
            ->slideOver()
            ->modalHeading('Ask PERSCOM')
            ->modalDescription('Ask about your personnel or describe changes to prepare, such as "Promote everyone in 2nd Platoon with 30 days in grade". Nothing is changed until you review and apply it.')
            ->modalSubmitActionLabel('Ask')
            ->schema([
                Textarea::make('prompt')
                    ->label('What do you need?')
                    ->required()
                    ->rows(5)
                    ->maxLength(2000),
            ])
            ->action(function (array $data, Action $action): void {
                try {
                    $result = CopilotService::ask($data['prompt'], Auth::user());
                } catch (AiUsageLimitExceededException $exception) {
                    Notification::make()->danger()->title('AI Limit Reached')->body($exception->getMessage())->send();
                    $action->halt();

                    return;
                } catch (Throwable $exception) {
                    Log::error('The copilot request failed.', ['exception' => $exception]);
                    Notification::make()->danger()->title('Something Went Wrong')->body('The copilot could not answer right now. Please try again.')->send();
                    $action->halt();

                    return;
                }

                $this->answer = $result['answer'];
                $this->changeSet = $result['changeSet']->toArray();

                $this->replaceMountedAction('review');
            });
    }

    public function reviewAction(): Action
    {
        return Action::make('review')
            ->slideOver()
            ->modalHeading('Copilot Response')
            ->modalContent(fn (): View => view('livewire.filament.app.copilot-review', [
                'answer' => $this->answer,
                'operations' => $this->proposedChangeSet()->operations,
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->extraModalFooterActions(fn (): array => $this->proposedChangeSet()->isEmpty() ? [] : [
                $this->applyChangesAction(),
            ]);
    }

    public function applyChangesAction(): Action
    {
        return Action::make('applyChanges')
            ->label('Apply Changes')
            ->color('primary')
            ->requiresConfirmation()
            ->modalHeading('Apply Proposed Changes')
            ->modalDescription('The proposed records will be created and members will be notified as usual. This cannot be undone automatically.')
            ->modalSubmitActionLabel('Apply Changes')
            ->cancelParentActions()
            ->action(function (Action $action): void {
                try {
                    $result = ApplyChangeSet::handle($this->proposedChangeSet(), Auth::user());
                } catch (AuthorizationException) {
                    Notification::make()->danger()->title('Not Allowed')->body('You are not allowed to make one or more of these changes. Nothing was applied.')->send();
                    $action->halt();

                    return;
                } catch (ValidationException $exception) {
                    Notification::make()->danger()->title('Changes Not Applied')->body(collect($exception->errors())->flatten()->first())->send();
                    $action->halt();

                    return;
                }

                $this->reset(['answer', 'changeSet']);

                Notification::make()->success()->title('Changes Applied')->body(sprintf('%d record(s) were created.', $result['created']))->send();
            });
    }

    public function proposedChangeSet(): ProposedChangeSetData
    {
        return ProposedChangeSetData::from(filled($this->changeSet) ? $this->changeSet : ['operations' => []]);
    }
}
