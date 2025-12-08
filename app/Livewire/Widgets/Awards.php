<?php

declare(strict_types=1);

namespace App\Livewire\Widgets;

use App\Models\Award;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Awards extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.widgets.awards');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Award::query())
            ->heading('Awards')
            ->paginated()
            ->columns([
                Split::make([
                    ImageColumn::make('image.image_url')
                        ->placeholder('No Image')
                        ->visible(fn (?Award $record) => filled($record?->image))
                        ->grow(false)
                        ->disk('s3'),
                    Stack::make([
                        TextColumn::make('name')
                            ->weight(FontWeight::Bold),
                        TextColumn::make('description')
                            ->placeholder('No Description')
                            ->html(),
                    ]),
                ])->from('sm'),
            ]);
    }
}
