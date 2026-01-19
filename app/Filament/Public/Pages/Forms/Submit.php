<?php

declare(strict_types=1);

namespace App\Filament\Public\Pages\Forms;

use App\Models\Form as FormModel;
use App\Models\Submission;
use App\Traits\Filament\BuildsCustomFieldComponents;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @property Schema $form
 */
class Submit extends Page implements HasForms
{
    use BuildsCustomFieldComponents;
    use InteractsWithFormActions;
    use InteractsWithForms;

    public FormModel $submissionForm;

    public ?array $data = null;

    public bool $submitted = false;

    protected static ?string $slug = 'forms/{slug}';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Submit Form';

    protected string $view = 'filament.public.pages.forms.submit';

    public function mount(string $slug): void
    {
        $form = FormModel::where('slug', $slug)->first();

        if (! $form instanceof FormModel || ! $form->is_public) {
            throw new NotFoundHttpException('Form not found');
        }

        $this->submissionForm = $form;

        $this->form->fill();
    }

    public function getTitle(): string|Htmlable
    {
        return $this->submissionForm->name ?? 'Submit Form';
    }

    public function getSubheading(): string|Htmlable|null
    {
        if ($this->submitted) {
            return null;
        }

        return new HtmlString($this->submissionForm->description ?? '');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components(self::buildCustomFieldInputs($this->submissionForm->fields));
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        Submission::create(array_merge([
            'form_id' => $this->submissionForm->getKey(),
        ], data_get($data, 'data', [])));

        $this->submitted = true;

        Notification::make()
            ->success()
            ->title('Form Submitted')
            ->body($this->submissionForm->success_message ?? 'Your form has been successfully submitted.')
            ->send();
    }

    public function hasLogo(): bool
    {
        return true;
    }

    /**
     * @return Action[]
     */
    protected function getFormActions(): array
    {
        if ($this->submitted) {
            return [];
        }

        return [
            $this->getSubmitFormAction(),
        ];
    }

    protected function getSubmitFormAction(): Action
    {
        return Action::make('submit')
            ->label('Submit')
            ->submit('submit');
    }
}
