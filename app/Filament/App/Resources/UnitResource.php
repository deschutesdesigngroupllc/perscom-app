<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\UnitResource\Pages\CreateUnit;
use App\Filament\App\Resources\UnitResource\Pages\EditUnit;
use App\Filament\App\Resources\UnitResource\Pages\ListUnits;
use App\Filament\App\Resources\UnitResource\RelationManagers\SlotsRelationManager;
use App\Filament\Exports\UnitExporter;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use App\Models\Unit;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Guava\IconPicker\Forms\Components\IconPicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use UnitEnum;

class UnitResource extends BaseResource
{
    protected static ?string $model = Unit::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Unit')
                            ->icon('heroicon-o-home')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the unit.')
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->helperText('A brief description of the unit.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Image')
                            ->visibleOn('edit')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make()
                                    ->hiddenLabel()
                                    ->relationship('image', fn ($state) => filled(data_get($state, 'path')))
                                    ->schema([
                                        FileUpload::make('path')
                                            ->hiddenLabel()
                                            ->image()
                                            ->imageEditor()
                                            ->previewable()
                                            ->openable()
                                            ->downloadable()
                                            ->visibility('public')
                                            ->storeFileNamesIn('filename')
                                            ->helperText('Add an optional image for the unit.'),
                                    ]),
                            ]),
                        Tab::make('Roster')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Toggle::make('hidden')
                                    ->helperText('Hide this unit from the roster.')
                                    ->required(),
                                IconPicker::make('icon')
                                    ->helperText(new HtmlString('An optional icon for the unit. A list of icons can be found <a href="https://heroicons.com/" target="_blank" class="underline">here</a>.')),
                                RichEditor::make('empty')
                                    ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                                    ->label('Empty Message')
                                    ->helperText('Display a message when no users or slots occupy the unit.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no units to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image.path')
                    ->placeholder('No Image')
                    ->label('Image'),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                ToggleColumn::make('hidden')
                    ->sortable(),
                IconColumn::make('icon')
                    ->placeholder('No Icon')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordClasses(fn (?Unit $record): ?string => match ($record->hidden) {
                true => 'border-s-2! border-s-red-600!',
                default => null,
            })
            ->groups(['hidden'])
            ->filters([
                TernaryFilter::make('hidden'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(UnitExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                VisibleScope::class,
                HiddenScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SlotsRelationManager::class,
        ];
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListUnits::route('/'),
            'create' => CreateUnit::route('/create'),
            'edit' => EditUnit::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Unit  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Unit  $record
     * @return string[]
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        if (blank($record->description)) {
            return [];
        }

        return [
            Str::of($record->description)->stripTags()->limit()->squish()->toString(),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
