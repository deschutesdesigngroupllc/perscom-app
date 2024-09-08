<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Contracts\HasFields;
use App\Filament\App\Resources\FormResource;
use App\Models\Form as FormModel;
use App\Models\Submission;
use App\Traits\Filament\InteractsWithFields;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\HasUnsavedDataChangesAlert;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Gate;

/**
 * @property Form $form
 */
class SubmitForm extends Page implements HasForms
{
    use HasUnsavedDataChangesAlert;
    use InteractsWithFields;
    use InteractsWithFormActions;
    use InteractsWithForms;
    use InteractsWithRecord;

    public ?array $data;

    protected static string $resource = FormResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.app.pages.forms.submit';

    protected static ?string $navigationLabel = 'Forms';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Forms';

    public static function authorizeResourceAccess(): void
    {
        abort_unless(Gate::check('create', Submission::class), 403);
    }

    public function getBreadcrumb(): string
    {
        /** @var FormModel $formModel */
        $formModel = $this->getRecord();

        return $formModel->name;
    }

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function form(Form $form): Form
    {
        /** @var HasFields $record */
        $record = $this->getRecord();

        return $form
            ->statePath('data')
            ->schema([
                Section::make($record->name)
                    ->schema(SubmitForm::getFormSchemaFromFields($record)),
            ]);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();

        /** @var FormModel $formModel */
        $formModel = $this->getRecord();

        Submission::create(array_merge([
            'form_id' => $formModel->getKey(),
        ], data_get($data, 'data')));

        $this->getSavedNotification()->send();

        $this->redirect(FormResource::getUrl('list'));
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title('Your form has been successfully submitted.');
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
