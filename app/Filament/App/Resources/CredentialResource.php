<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\CredentialResource\Pages;
use App\Models\Credential;
use App\Models\Enums\CredentialType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class CredentialResource extends BaseResource
{
    protected static ?string $model = Credential::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Training';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Credential')
                            ->columns()
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The name of the credential.')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('type')
                                    ->helperText('The type of the credential.')
                                    ->options(CredentialType::class)
                                    ->required(),
                                Forms\Components\Select::make('issuer_id')
                                    ->helperText('The issuer of the credential.')
                                    ->searchable()
                                    ->relationship('issuer', 'name')
                                    ->required(),
                                Forms\Components\RichEditor::make('description')
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
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('issuer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('issuer')
                    ->relationship('issuer', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('type')
                    ->options(CredentialType::class)
                    ->multiple()
                    ->preload(),
            ])
            ->groups(['issuer.name', 'type'])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListCredentials::route('/'),
            'create' => Pages\CreateCredential::route('/create'),
            'edit' => Pages\EditCredential::route('/{record}/edit'),
        ];
    }
}
