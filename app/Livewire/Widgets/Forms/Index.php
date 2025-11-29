<?php

declare(strict_types=1);

namespace App\Livewire\Widgets\Forms;

use App\Models\Form;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.widgets.forms.index');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Form::query())
            ->heading('Forms')
            ->paginated()
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold),
                    TextColumn::make('description')
                        ->html(),
                ]),
            ])
            ->recordActions([
                Action::make('open')
                    ->button()
                    ->color('primary')
                    ->action(function (Form $record): void {
                        $this->dispatch('iframe:navigate', path: 'forms/'.$record->getKey());
                    }),
            ]);
    }
}
