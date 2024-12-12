<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TenantResource\Pages;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\DomainsRelationManager;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\SubscriptionsRelationManager;
use App\Models\Tenant;
use App\Models\User;
use App\Rules\SubdomainRule;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\GlobalSearch\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $recordTitleAttribute = 'name';

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
                        Forms\Components\Tabs\Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\DateTimePicker::make('last_login_at')
                                    ->helperText('The last time a user logged into the tenant account.')
                                    ->nullable(),
                                Forms\Components\DateTimePicker::make('setup_completed_at')
                                    ->helperText('The time the account completed initial setup.')
                                    ->nullable(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Billing')
                            ->visible(fn ($operation) => $operation !== 'create')
                            ->icon('heroicon-o-credit-card')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('invoice_emails')
                                    ->columnSpanFull()
                                    ->maxLength(255)
                                    ->label('Invoice Emails')
                                    ->helperText('Separate using a column for multiple email addresses.')
                                    ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                                Forms\Components\TextInput::make('billing_address')
                                    ->maxLength(255)
                                    ->label('Address')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_address_line_2')
                                    ->maxLength(255)
                                    ->label('Address Line 2')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_city')
                                    ->maxLength(255)
                                    ->label('City')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_state')
                                    ->maxLength(255)
                                    ->label('State')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_postal_code')
                                    ->maxLength(255)
                                    ->label('Postal Code')
                                    ->disabled(),
                                Forms\Components\TextInput::make('billing_country')
                                    ->maxLength(255)
                                    ->label('Country')
                                    ->disabled(),
                                Forms\Components\TextInput::make('vat_id')
                                    ->maxLength(255)
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
                                    ->maxLength(255)
                                    ->label('Database Name')
                                    ->disabled(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Payment')
                            ->visible(fn ($operation) => $operation !== 'create')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('stripe_id')
                                    ->maxLength(255)
                                    ->label('Stripe ID')
                                    ->disabled(),
                                Forms\Components\TextInput::make('pm_type')
                                    ->maxLength(255)
                                    ->label('Payment Method Type')
                                    ->disabled(),
                                Forms\Components\TextInput::make('pm_last_four')
                                    ->maxLength(255)
                                    ->label('Payment Method Last Four')
                                    ->disabled(),
                                Forms\Components\TextInput::make('pm_expiration')
                                    ->maxLength(255)
                                    ->label('Payment Method Expiration')
                                    ->disabled(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Metadata')
                            ->visible(fn ($operation) => $operation !== 'create')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                CodeEditor::make('data')
                                    ->dehydrated(false)
                                    ->hiddenLabel()
                                    ->formatStateUsing(fn (?Tenant $record) => json_encode(json_decode($record?->getRawOriginal('data') ?? ''), JSON_PRETTY_PRINT))
                                    ->json(),
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
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('URL'))
                    ->copyable()
                    ->sortable()
                    ->openUrlInNewTab()
                    ->url(fn ($state) => $state),
                Tables\Columns\TextColumn::make('subscription_plan')
                    ->label('Subscription')
                    ->badge(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('login')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('gray')
                    ->modalDescription('Login to the tenant using the user below.')
                    ->form([
                        Forms\Components\Select::make('user')
                            ->searchable()
                            ->helperText('Select the user to login as.')
                            ->options(function (Tenant $record) {
                                return $record->run(function () {
                                    return User::query()->orderBy('name')->whereHas('roles', function (Builder $query) {
                                        $query->where('name', Utils::getSuperAdminName());
                                    })->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required(),
                    ])
                    ->action(function (Tables\Actions\Action $action, Tenant $record, array $data) {
                        if (! method_exists(tenancy(), 'impersonate')) {
                            return null;
                        }

                        $token = tenancy()->impersonate($record, data_get($data, 'user'), '/', 'web');

                        return redirect()->to($record->route('tenant.impersonation', [
                            'token' => $token,
                        ]));
                    }),
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

    /**
     * @param  Tenant  $record
     */
    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }

    /**
     * @return string[]
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'name', 'email', 'website', 'domains.domain'];
    }

    /**
     * @param  Tenant  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'ID' => $record->id,
            'Tenant' => $record->name,
            'Email' => $record->email,
            'URL' => $record->url,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['domains']);
    }

    /**
     * @param  Tenant  $record
     */
    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('dashboard')
                ->openUrlInNewTab()
                ->url(fn () => $record->url),
        ];
    }
}
