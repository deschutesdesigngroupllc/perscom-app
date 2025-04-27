<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\IssuerResource\Pages;
use App\Filament\Exports\IssuerExporter;
use App\Models\Issuer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class IssuerResource extends BaseResource
{
    protected static ?string $model = Issuer::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Training';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Issuer')
                            ->columns()
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the issuer.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(IssuerExporter::class)
                    ->icon('heroicon-o-document-arrow-down'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIssuers::route('/'),
            'create' => Pages\CreateIssuer::route('/create'),
            'edit' => Pages\EditIssuer::route('/{record}/edit'),
        ];
    }
}
