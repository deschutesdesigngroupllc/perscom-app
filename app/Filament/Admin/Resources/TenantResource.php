<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TenantResource\Pages;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\DomainsRelationManager;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\SubscriptionsRelationManager;
use App\Models\Tenant;
use App\Rules\SubdomainRule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Application';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Tenant')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->helperText('The tenant\'s name.')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->helperText('The tenant\'s email.')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('domain')
                                    ->helperText('The tenant\'s initial fallback subdomain.')
                                    ->required()
                                    ->visibleOn('create')
                                    ->rule(new SubdomainRule)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('website')
                                    ->helperText('The tenant\'s website.')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Tabs\Tab::make('Billing')
                            ->visible(fn ($operation) => $operation !== 'create')
                            ->icon('heroicon-o-credit-card')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('billing_address')
                                    ->label('Address')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_address_line_2')
                                    ->label('Address Line 2')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_city')
                                    ->label('City')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_state')
                                    ->label('State')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_postal_code')
                                    ->label('Postal Code')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_country')
                                    ->label('Country')
                                    ->disabled(),
                                Forms\Components\TextInput::make('vat_id')
                                    ->label('VAT ID')
                                    ->disabled()
                                    ->columnSpanFull(),
                                Forms\Components\DateTimePicker::make('trial_ends_at')
                                    ->label('Trial Ends At')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Database')
                            ->visible(fn ($operation) => $operation !== 'create')
                            ->icon('heroicon-o-circle-stack')
                            ->schema([
                                Forms\Components\TextInput::make('tenancy_db_name')
                                    ->label('Database Name')
                                    ->disabled(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Payment')
                            ->visible(fn ($operation) => $operation !== 'create')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('stripe_id')
                                    ->label('Stripe ID')
                                    ->disabled(),
                                Forms\Components\TextInput::make('pm_type')
                                    ->label('Payment Method Type')
                                    ->disabled(),
                                Forms\Components\TextInput::make('pm_last_four')
                                    ->label('Payment Method Last Four')
                                    ->disabled(),
                                Forms\Components\TextInput::make('pm_expiration')
                                    ->label('Payment Method Expiration')
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('PERSCOM ID'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('URL'))
                    ->copyable()
                    ->sortable()
                    ->openUrlInNewTab()
                    ->url(fn ($state) => $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DomainsRelationManager::make(),
            SubscriptionsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
