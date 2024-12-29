<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\ExportDataFeature;
use App\Filament\App\Resources\UnitResource\Pages;
use App\Filament\Exports\UnitExporter;
use App\Models\Scopes\HiddenScope;
use App\Models\Scopes\VisibleScope;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;

class UnitResource extends BaseResource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Unit')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the unit.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\RichEditor::make('description')
                                    ->helperText('A brief description of the unit.')
                                    ->nullable()
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Image')
                            ->visibleOn('edit')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->hiddenLabel()
                                    ->relationship('image', fn ($state) => filled(data_get($state, 'path')))
                                    ->schema([
                                        Forms\Components\FileUpload::make('path')
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
                        Forms\Components\Tabs\Tab::make('Roster')
                            ->icon('heroicon-o-queue-list')
                            ->schema([
                                Forms\Components\Select::make('groups')
                                    ->preload()
                                    ->multiple()
                                    ->relationship('groups', 'name')
                                    ->helperText('The groups this unit belongs to.')
                                    ->createOptionForm(fn ($form) => GroupResource::form($form)),
                                Forms\Components\Toggle::make('hidden')
                                    ->helperText('Hide this unit from the roster.')
                                    ->required(),
                                IconPicker::make('icon')
                                    ->preload()
                                    ->helperText(new HtmlString('An optional icon for the unit. A list of icons can be found <a href="https://heroicons.com/" target="_blank" class="underline">here</a>.')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image.path')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('hidden')
                    ->sortable(),
                IconColumn::make('icon')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordClasses(fn (?Unit $record) => match ($record->hidden) {
                true => '!border-s-2 !border-s-red-600',
                default => null,
            })
            ->groups(['hidden'])
            ->filters([
                Tables\Filters\TernaryFilter::make('hidden'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportAction::make()
                        ->visible(Feature::active(ExportDataFeature::class))
                        ->exporter(UnitExporter::class),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
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
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Description' => Str::of($record->description)->stripTags()->limit()->squish()->toString(),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
