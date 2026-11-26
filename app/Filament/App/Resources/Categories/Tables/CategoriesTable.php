<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Categories\Tables;

use App\Models\Award;
use App\Models\Category;
use App\Models\Competency;
use App\Models\Document;
use App\Models\Form;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Specialty;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateIcon(Heroicon::OutlinedTag)
            ->emptyStateDescription('Categories group awards, ranks, documents and more. Add one to start organizing.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('resource')
                    ->options([
                        Award::class => 'Awards',
                        Competency::class => 'Competencies',
                        Document::class => 'Documents',
                        Form::class => 'Forms',
                        Position::class => 'Positions',
                        Qualification::class => 'Qualifications',
                        Rank::class => 'Ranks',
                        Specialty::class => 'Specialties',
                    ]),
            ])
            ->groups([
                Group::make('resource')
                    ->getTitleFromRecordUsing(fn (Category $record): string => Str::of($record->resource)->classBasename()->plural()->toString()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('resource');
    }
}
