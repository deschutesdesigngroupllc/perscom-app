<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\RankResource\Pages\CreateRank;
use App\Filament\App\Resources\RankResource\Pages\EditRank;
use App\Filament\App\Resources\RankResource\Pages\ListRanks;
use App\Filament\Exports\RankExporter;
use App\Models\Rank;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class RankResource extends BaseResource
{
    protected static ?string $model = Rank::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chevron-double-up';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Rank')
                            ->icon('heroicon-o-chevron-double-up')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the rank.')
                                    ->columnSpanFull()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('abbreviation')
                                    ->helperText('The abbreviation of the rank.')
                                    ->nullable()
                                    ->maxLength(255),
                                TextInput::make('paygrade')
                                    ->helperText('The paygrade of the rank.')
                                    ->nullable()
                                    ->maxLength(255),
                                RichEditor::make('description')
                                    ->helperText('A brief description of the rank.')
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
                                            ->helperText('Add an optional image for the rank.'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image.path')
                    ->label('Image'),
                TextColumn::make('abbreviation')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('paygrade')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(RankExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRanks::route('/'),
            'create' => CreateRank::route('/create'),
            'edit' => EditRank::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Rank  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Rank  $record
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }
}
