<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\StatusResource\Pages\CreateStatus;
use App\Filament\App\Resources\StatusResource\Pages\EditStatus;
use App\Filament\App\Resources\StatusResource\Pages\ListStatuses;
use App\Filament\Exports\StatusExporter;
use App\Models\Status;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class StatusResource extends BaseResource
{
    protected static ?string $model = Status::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|UnitEnum|null $navigationGroup = 'Organization';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->helperText('The name of the status.')
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('color')
                    ->helperText('The color of the status.')
                    ->required(),
                IconPicker::make('icon')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no statuses to view. Create one to get started.')
            ->columns([
                TextColumn::make('name')
                    ->badge()
                    ->color(fn (?Status $record): array => Color::generateV3Palette($record->color))
                    ->sortable()
                    ->searchable(),
                ColorColumn::make('color')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('icon')
                    ->sortable(),
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
                    ->exporter(StatusExporter::class)
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
            'index' => ListStatuses::route('/'),
            'create' => CreateStatus::route('/create'),
            'edit' => EditStatus::route('/{record}/edit'),
        ];
    }

    /**
     * @param  Status  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
