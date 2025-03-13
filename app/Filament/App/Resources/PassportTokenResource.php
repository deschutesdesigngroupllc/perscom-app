<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PassportTokenResource\Pages;
use App\Models\PassportToken;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Laravel\Passport\Passport;

class PassportTokenResource extends BaseResource
{
    protected static ?string $model = PassportToken::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static ?string $pluralModelLabel = 'API keys';

    protected static ?string $modelLabel = 'API key';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('API Key Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->helperText('An identifying name for the API key')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('scopes')
                            ->helperText(fn ($operation): string => match ($operation) {
                                'edit' => 'Please create a new API key to change the scopes.',
                                default => 'The scopes that the API key will have access to.'
                            })
                            ->multiple()
                            ->live()
                            ->disabled(fn ($operation): bool => $operation !== 'create')
                            ->options(fn () => Passport::scopes()->pluck('id', 'id')->sort())
                            ->hidden(fn (Forms\Get $get): mixed => $get('all_scopes')),
                        Forms\Components\Checkbox::make('all_scopes')
                            ->visibleOn('create')
                            ->default(true)
                            ->live()
                            ->inline()
                            ->helperText('Select to allow access to all scopes.'),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('name'),
                TextEntry::make('expires_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime(),
                TextEntry::make('token')
                    ->label('API key')
                    ->badge()
                    ->color('gray')
                    ->helperText('Click to copy the API key to your clipboard.')
                    ->copyable()
                    ->columnSpan(1),
                TextEntry::make('scopes')
                    ->columnSpanFull()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->badge(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('Create your first API key to start integrating with PERSCOM\'s powerful API.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scopes')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPassportTokens::route('/'),
            'create' => Pages\CreatePassportToken::route('/create'),
            'view' => Pages\ViewPassportToken::route('/{record}'),
            'edit' => Pages\EditPassportToken::route('/{record}/edit'),
        ];
    }
}
