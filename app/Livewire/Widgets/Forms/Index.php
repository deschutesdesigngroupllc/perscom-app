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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
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
            ->emptyStateIcon(Heroicon::OutlinedPencilSquare)
            ->emptyStateDescription('There are no forms to display.')
            ->query(Form::query())
            ->heading('Forms')
            ->paginated()
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->toggleable(false)
                        ->weight(FontWeight::Bold),
                    TextColumn::make('description')
                        ->toggleable(false)
                        ->placeholder('No Description')
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
            ])
            ->groups([
                Group::make('categoryPivot.category_id')
                    ->titlePrefixedWithLabel(false)
                    ->label('Category')
                    ->getTitleFromRecordUsing(fn (Form $record) => $record->categoryPivot?->category?->name),
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('categoryPivot.category_id');
    }
}
