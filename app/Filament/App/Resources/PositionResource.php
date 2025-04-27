<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PositionResource\Pages;
use App\Filament\Exports\PositionExporter;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PositionResource extends BaseResource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Position Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->helperText('The name of the position.')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->helperText('A brief description of the position.')
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(fn ($state) => Str::limit($state))
                    ->html()
                    ->wrap()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->filters([
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(PositionExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->reorderable('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Position  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Position  $record
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
