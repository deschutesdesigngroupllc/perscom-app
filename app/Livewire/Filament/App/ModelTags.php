<?php

declare(strict_types=1);

namespace App\Livewire\Filament\App;

use App\Models\Enums\ModelTagType;
use App\Models\ModelTag;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ModelTags extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public function render(): View
    {
        return view('livewire.filament.app.model-tags');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ModelTag::query())
            ->columns([
                Split::make([
                    TextColumn::make('tag')
                        ->copyable()
                        ->searchable(),
                    TextColumn::make('description')
                        ->searchable()
                        ->color('gray'),
                ])->from('sm'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(ModelTagType::class)
                    ->multiple()
                    ->searchable(),
            ])
            ->defaultGroup('type')
            ->groups([
                Group::make('type')
                    ->getDescriptionFromRecordUsing(fn (ModelTag $record) => $record->type->getDescription()),
            ])
            ->paginated(false);
    }
}
