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
use Livewire\Features\SupportEvents\Event;

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
                        Placeholder::make('instructions')
                            ->hiddenLabel()
                            ->content(new HtmlString($formModel->instructions)),
                        Placeholder::make('success')
                            ->hiddenLabel()
                            ->visible(fn ($state) => filled($state))
                            ->content(fn ($state): HtmlString => new HtmlString(<<<HTML
<div class="text-green-600 font-bold">$state</div>
HTML
                            )),
                    ], Submit::getFormSchemaFromFields($this->record), [
                        Actions::make([
                            Actions\Action::make('back')
                                ->button()
                                ->color('gray')
                                ->action(fn (): Event => $this->dispatch('iframe:navigate', path: 'forms')),
                            Actions\Action::make('submit')
                                ->action('submitForm'),
                        ])->alignCenter(),
                    ])),
            ]);
    }

    public function submitForm(): void
    {
        $data = $this->form->getState();

        /** @var FormModel $form */
        $form = $this->record;
        $form->submissions()->create(data_get($data, 'data'));

        $this->form->fill([
            'success' => $form->success_message ?? null,
        ]);
    }
}
