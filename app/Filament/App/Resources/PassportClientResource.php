<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Features\OAuth2AccessFeature;
use App\Filament\App\Resources\PassportClientResource\Pages;
use App\Models\Enums\PassportClientType;
use App\Models\PassportClient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Passport;
use Laravel\Pennant\Feature;

class PassportClientResource extends BaseResource
{
    protected static ?string $model = PassportClient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static ?string $pluralModelLabel = 'clients';

    protected static ?string $modelLabel = 'client';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Application')
                            ->icon('heroicon-o-computer-desktop')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('An identifying name for the client.')
                                    ->maxLength(255)
                                    ->required(),
                                Forms\Components\Select::make('type')
                                    ->live()
                                    ->helperText('The type of client')
                                    ->options(PassportClientType::class)
                                    ->required(),
                                Forms\Components\TextInput::make('redirect')
                                    ->maxLength(255)
                                    ->label('Redirect URL')
                                    ->url()
                                    ->helperText('The URL to redirect to after authorization.')
                                    ->required(fn (Forms\Get $get) => in_array($get('type'), [PassportClientType::AUTHORIZATION_CODE->value, PassportClientType::IMPLICIT->value]))
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), [PassportClientType::AUTHORIZATION_CODE->value, PassportClientType::IMPLICIT->value])),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(65535)
                                    ->helperText('An optional description of the client'),
                                Forms\Components\Select::make('scopes')
                                    ->helperText('The scopes that the client application will have access to.')
                                    ->searchable()
                                    ->multiple()
                                    ->live()
                                    ->options(fn () => Passport::scopes()->pluck('id', 'id')->sort())
                                    ->hidden(fn (Forms\Get $get) => $get('all_scopes')),
                                Forms\Components\Checkbox::make('all_scopes')
                                    ->default(true)
                                    ->live()
                                    ->inline()
                                    ->helperText('Select to allow access to all scopes.'),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Client')
                        ->icon('heroicon-o-rectangle-stack')
                        ->schema([
                            TextEntry::make('id')
                                ->label('Client ID')
                                ->color('gray')
                                ->badge()
                                ->copyable(),
                            TextEntry::make('secret')
                                ->label('Client Secret')
                                ->color('gray')
                                ->badge()
                                ->copyable(),
                            TextEntry::make('redirect')
                                ->label('Redirect URL')
                                ->url(fn ($state) => $state)
                                ->copyable(),
                            TextEntry::make('revoked')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => match ($state) {
                                    '1', 1, true => 'danger',
                                    default => 'success'
                                })
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    '1', 1, true => 'Revoked',
                                    default => 'In Use'
                                }),
                            TextEntry::make('scopes')
                                ->columnSpanFull()
                                ->listWithLineBreaks()
                                ->limitList()
                                ->expandableLimitedList()
                                ->badge(),
                        ]),
                    Tabs\Tab::make('Endpoints')
                        ->icon('heroicon-o-link')
                        ->schema([
                            KeyValueEntry::make('endpoints')
                                ->hiddenLabel()
                                ->keyLabel('Endpoint')
                                ->valueLabel('URL')
                                ->getStateUsing(fn () => [
                                    'Discovery Endpoint' => route('oidc.discovery', [
                                        'tenant' => optional(tenant()->domain)->domain,
                                    ]),
                                    'Authorization Endpoint' => route('passport.authorizations.authorize', [
                                        'tenant' => optional(tenant()->domain)->domain,
                                    ]),
                                    'Token Endpoint' => route('passport.token', [
                                        'tenant' => optional(tenant()->domain)->domain,
                                    ]),
                                    'Logout Endpoint' => route('oidc.logout', [
                                        'tenant' => optional(tenant()->domain)->domain,
                                    ]),
                                    'User Info Endpoint' => route('oidc.userinfo', [
                                        'tenant' => optional(tenant()->domain)->domain,
                                    ]),
                                ]),
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
                Tables\Columns\TextColumn::make('id')
                    ->label('Client ID')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                Tables\Columns\TextColumn::make('secret')
                    ->label('Client Secret')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                Tables\Columns\TextColumn::make('scopes')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('revoked')
                    ->sortable(),
                Tables\Columns\TextColumn::make('redirect')
                    ->label('Redirect URL')
                    ->sortable()
                    ->url(fn ($state) => $state)
                    ->copyable(),
            ])
            ->filters([
                //
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('name', '<>', 'Default Personal Access Client')
            ->where('name', '<>', 'Default Password Grant Client');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPassportClients::route('/'),
            'create' => Pages\CreatePassportClient::route('/create'),
            'edit' => Pages\EditPassportClient::route('/{record}/edit'),
            'view' => Pages\ViewPassportClient::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(OAuth2AccessFeature::class);
    }
}
