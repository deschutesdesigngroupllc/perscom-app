<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SpecialtyResource\Pages\CreateSpecialty;
use App\Filament\App\Resources\SpecialtyResource\Pages\EditSpecialty;
use App\Filament\App\Resources\SpecialtyResource\Pages\ListSpecialties;
use App\Filament\Exports\SpecialtyExporter;
use App\Models\Specialty;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class SpecialtyResource extends BaseResource
{
    protected static ?string $model = Specialty::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Specialty Information')
                    ->schema([
                        TextInput::make('name')
                            ->helperText('The name of the specialty.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('abbreviation')
                            ->helperText('The abbreviation of the specialty.')
                            ->nullable()
                            ->maxLength(255),
                        RichEditor::make('description')
                            ->extraInputAttributes(['style' => 'min-height: 10rem;'])
                            ->helperText('A brief description of the specialty.')
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
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('abbreviation')
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
                    ->exporter(SpecialtyExporter::class)
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
            'index' => ListSpecialties::route('/'),
            'create' => CreateSpecialty::route('/create'),
            'edit' => EditSpecialty::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Specialty  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    /**
     * @param  Specialty  $record
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
