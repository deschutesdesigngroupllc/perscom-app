<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CompetencyResource\Pages\CreateCompetency;
use App\Filament\App\Resources\CompetencyResource\Pages\EditCompetency;
use App\Filament\App\Resources\CompetencyResource\Pages\ListCompetencies;
use App\Filament\Exports\CompetencyExporter;
use App\Models\Competency;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CompetencyResource extends BaseResource
{
    protected static ?string $model = Competency::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-numbered-list';

    protected static string|UnitEnum|null $navigationGroup = 'Training';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->helperText('The name of the competency.')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('categories')
                    ->label('Category')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        Hidden::make('resource')
                            ->default(static::$model),
                    ])
                    ->helperText('The category the competency belongs to.')
                    ->nullable()
                    ->preload()
                    ->searchable()
                    ->multiple()
                    ->maxItems(1)
                    ->relationship('categories', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('resource', static::$model)),
                RichEditor::make('description')
                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                    ->helperText('A brief description of the competency.')
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no competencies to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('categories.name')
                    ->placeholder('No Categories')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('categories.name')
                    ->relationship('categories', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->groups([
                Group::make('categoryPivot.category_id')
                    ->label('Category')
                    ->getTitleFromRecordUsing(fn (Competency $record) => $record->categoryPivot?->category?->name),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(CompetencyExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultGroup('categoryPivot.category_id');
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListCompetencies::route('/'),
            'create' => CreateCompetency::route('/create'),
            'edit' => EditCompetency::route('/{record}/edit'),
        ];
    }
}
