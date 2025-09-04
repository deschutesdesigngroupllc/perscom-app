<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CredentialResource\Pages\CreateCredential;
use App\Filament\App\Resources\CredentialResource\Pages\EditCredential;
use App\Filament\App\Resources\CredentialResource\Pages\ListCredentials;
use App\Filament\Exports\CredentialExporter;
use App\Models\Credential;
use App\Models\Enums\CredentialType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportBulkAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class CredentialResource extends BaseResource
{
    protected static ?string $model = Credential::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|UnitEnum|null $navigationGroup = 'Training';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Credential')
                            ->columns()
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The name of the credential.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Select::make('type')
                                    ->helperText('The type of the credential.')
                                    ->options(CredentialType::class)
                                    ->required(),
                                Select::make('issuer_id')
                                    ->helperText('The issuer of the credential.')
                                    ->searchable()
                                    ->relationship('issuer', 'name')
                                    ->required(),
                                RichEditor::make('description')
                                    ->helperText('A brief description of the credential.')
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
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('issuer.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('issuer')
                    ->relationship('issuer', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('type')
                    ->options(CredentialType::class)
                    ->multiple()
                    ->preload(),
            ])
            ->groups(['issuer.name', 'type'])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                ExportBulkAction::make()
                    ->exporter(CredentialExporter::class)
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
            'index' => ListCredentials::route('/'),
            'create' => CreateCredential::route('/create'),
            'edit' => EditCredential::route('/{record}/edit'),
        ];
    }
}
