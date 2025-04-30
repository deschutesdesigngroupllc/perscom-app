<?php

declare(strict_types=1);

namespace App\Livewire\Widgets\Forms;

use App\Filament\App\Pages\Forms\Submit;
use App\Models\Form as FormModel;
use App\Models\Submission;
use App\Traits\Filament\InteractsWithFields;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;

/**
 * @property Form $form;
 */
class Create extends Component implements HasForms
{
    use InteractsWithFields;
    use InteractsWithForms;
    use InteractsWithRecord;

    public ?array $data = null;

    public function mount(int|string $record): void
    {
        $this->record = FormModel::findOrFail($record);
        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.widgets.forms.create');
    }

    public function form(Form $form): Form
    {
        /** @var FormModel $formModel */
        $formModel = $this->record;

        return $form
            ->model(Submission::class)
            ->statePath('data')
            ->schema([
                Section::make($formModel->name)
                    ->description($formModel->description)
                    ->schema(array_merge([
                        Placeholder::make('Instructions')
                            ->hiddenLabel()
                            ->content(new HtmlString($formModel->instructions)),
                    ], Submit::getFormSchemaFromFields($this->record), [
                        Actions::make([
                            Actions\Action::make('submit')
                                ->action('submitForm'),
                        ])->alignCenter(),
                    ])),
            ]);
    }

    public function submitForm(): void
    {
        $data = $this->form->getState();

        dd($data);
    }
}
