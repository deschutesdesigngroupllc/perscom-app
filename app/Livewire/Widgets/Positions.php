<?php

declare(strict_types=1);

namespace App\Livewire\Widgets;

use App\Models\Position;
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

class Positions extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.widgets.positions');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Position::query())
            ->heading('Positions')
            ->paginated()
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->weight(FontWeight::Bold),
                    TextColumn::make('description')
                        ->html(),
                ]),
            ]);
    }
}
