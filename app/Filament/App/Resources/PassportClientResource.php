<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PassportClientResource\Pages\CreatePassportClient;
use App\Filament\App\Resources\PassportClientResource\Pages\EditPassportClient;
use App\Filament\App\Resources\PassportClientResource\Pages\ListPassportClients;
use App\Filament\App\Resources\PassportClientResource\Pages\ViewPassportClient;
use App\Models\Enums\PassportClientType;
use App\Models\PassportClient;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Passport;
use UnitEnum;

class PassportClientResource extends BaseResource
{
    protected static ?string $model = PassportClient::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 9;

    protected static ?string $pluralModelLabel = 'clients';

    protected static ?string $modelLabel = 'client';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->helperText('An identifying name for the client.')
                    ->maxLength(255)
                    ->required(),
                Radio::make('type')
                    ->disabled(fn ($operation): bool => $operation !== 'create')
                    ->live()
                    ->options(PassportClientType::class)
                    ->required(),
                TextInput::make('redirect')
                    ->maxLength(255)
                    ->label('Redirect URL')
                    ->url()
                    ->helperText('The URL to redirect to after authorization.')
                    ->required(fn (Get $get): bool => in_array($get('type'), [PassportClientType::AUTHORIZATION_CODE, PassportClientType::IMPLICIT]))
                    ->visible(fn (Get $get): bool => in_array($get('type'), [PassportClientType::AUTHORIZATION_CODE, PassportClientType::IMPLICIT])),
                Textarea::make('description')
                    ->maxLength(65535)
                    ->helperText('An optional description of the client'),
                Select::make('scopes')
                    ->helperText('The scopes that the client application will have access to.')
                    ->searchable()
                    ->multiple()
                    ->live()
                    ->options(fn () => Passport::scopes()->pluck('id', 'id')->sort())
                    ->hidden(fn (Get $get): mixed => $get('all_scopes')),
                Checkbox::make('all_scopes')
                    ->default(true)
                    ->live()
                    ->inline()
                    ->helperText('Select to allow access to all scopes.'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Client')
                        ->icon('heroicon-o-rectangle-stack')
                        ->schema([
                            TextEntry::make('name'),
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
                            TextEntry::make('type')
                                ->badge(),
                            TextEntry::make('redirect')
                                ->visible(fn (PassportClient $record): bool => in_array($record->type, [PassportClientType::AUTHORIZATION_CODE, PassportClientType::IMPLICIT]))
                                ->label('Redirect URL')
                                ->url(fn ($state) => $state)
                                ->copyable(),
                            TextEntry::make('revoked')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state): string => match ($state) {
                                    '1', 1, true => 'danger',
                                    default => 'success'
                                })
                                ->formatStateUsing(fn ($state): string => match ($state) {
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
                    Tab::make('Endpoints')
                        ->icon('heroicon-o-link')
                        ->schema([
                            KeyValueEntry::make('endpoints')
                                ->hiddenLabel()
                                ->keyLabel('Endpoint')
                                ->valueLabel('URL')
                                ->getStateUsing(fn (): array => [
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
            ->emptyStateDescription("Create your first OAuth 2.0 client to start integrating with PERSCOM's powerful API.")
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('id')
                    ->label('Client ID')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                TextColumn::make('secret')
                    ->label('Client Secret')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->copyable(),
                TextColumn::make('scopes')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->searchable()
                    ->sortable(),
                ToggleColumn::make('revoked')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->preload()
                    ->multiple()
                    ->options(PassportClientType::class),
            ])
            ->groups(['type'])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('name', '<>', PassportClient::SYSTEM_PERSONAL_ACCESS_CLIENT)
            ->where('name', '<>', PassportClient::SYSTEM_PASSWORD_GRANT_CLIENT);
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListPassportClients::route('/'),
            'create' => CreatePassportClient::route('/create'),
            'edit' => EditPassportClient::route('/{record}/edit'),
            'view' => ViewPassportClient::route('/{record}'),
        ];
    }
}
