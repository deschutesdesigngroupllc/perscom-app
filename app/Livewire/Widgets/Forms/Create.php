<?php

declare(strict_types=1);

namespace App\Livewire\Widgets\Forms;

use App\Filament\App\Pages\Forms\Submit;
use App\Models\Form as FormModel;
use App\Models\Submission;
use App\Traits\Filament\BuildsCustomFieldComponents;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\Features\SupportEvents\Event;

/**
 * @property Schema $form ;
 */
class Create extends Component implements HasForms
{
    use BuildsCustomFieldComponents;
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

    public function form(Schema $schema): Schema
    {
        /** @var FormModel $formModel */
        $formModel = $this->record;

        return $schema
            ->model(Submission::class)
            ->operation('create')
            ->statePath('data')
            ->components([
                Section::make($formModel->name)
                    ->description($formModel->description)
                    ->schema(array_merge([
                        TextEntry::make('instructions')
                            ->hiddenLabel()
                            ->getStateUsing(fn (): HtmlString => new HtmlString($formModel->instructions)),
                        TextEntry::make('success')
                            ->hiddenLabel()
                            ->visible(fn ($state): bool => filled($state))
                            ->getStateUsing(fn ($state): HtmlString => new HtmlString(<<<HTML
<div class="text-green-600 font-bold">{$state}</div>
HTML
                            )),
                    ], Submit::buildCustomFieldInputs($formModel->fields), [
                        Actions::make([
                            Action::make('back')
                                ->button()
                                ->color('gray')
                                ->action(fn (): Event => $this->dispatch('iframe:navigate', path: 'forms')),
                            Action::make('submit')
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
