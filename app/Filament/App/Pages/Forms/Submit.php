<?php

declare(strict_types=1);

namespace App\Filament\App\Pages\Forms;

use App\Models\Form as FormModel;
use App\Models\Submission;
use App\Traits\Filament\InteractsWithFields;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

/**
 * @property Form $form
 */
class Submit extends Page implements HasForms
{
    use HasPageShield;
    use HasUnsavedDataChangesAlert;
    use InteractsWithFields;
    use InteractsWithFormActions;
    use InteractsWithForms;

    public FormModel $submissionForm;

    public ?array $data = null;

    protected static ?string $slug = '/forms/submit/{record}';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.forms.submit';

    protected static ?string $navigationLabel = 'Forms';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Submit Form';

    public function mount(int|string $record): void
    {
        $this->submissionForm = FormModel::findOrFail($record);

        $this->form->fill();
    }

    public function getTitle(): string|Htmlable
    {
        return $this->submissionForm->name ?? 'Submit Form';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString($this->submissionForm->description ?? '');
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema(Submit::getFormSchemaFromFields($this->submissionForm));
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        Submission::create(array_merge([
            'form_id' => $this->submissionForm->getKey(),
        ], data_get($data, 'data', [])));

        $this->getSavedNotification()->send();

        $this->redirect(Forms::getUrl());
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title($this->submissionForm->success_message ?? 'Your form has been successfully submitted.');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label('Submit')
            ->submit('save')
            ->keyBindings(['mod+s']);
    }
}
