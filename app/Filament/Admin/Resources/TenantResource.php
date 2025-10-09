<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TenantResource\Actions\SwapSubscriptionBulkAction;
use App\Filament\Admin\Resources\TenantResource\Pages\CreateTenant;
use App\Filament\Admin\Resources\TenantResource\Pages\EditTenant;
use App\Filament\Admin\Resources\TenantResource\Pages\ListTenants;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\DomainsRelationManager;
use App\Filament\Admin\Resources\TenantResource\RelationManagers\SubscriptionsRelationManager;
use App\Models\Enums\SubscriptionStatus;
use App\Models\Tenant;
use App\Models\User;
use App\Rules\SubdomainRule;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use UnitEnum;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|UnitEnum|null $navigationGroup = 'Application';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Tenant')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                TextInput::make('name')
                                    ->helperText('The tenant\'s name.')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->helperText('The tenant\'s email.')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('domain')
                                    ->helperText('The tenant\'s initial fallback subdomain.')
                                    ->required()
                                    ->visibleOn('create')
                                    ->rule(new SubdomainRule)
                                    ->prefix(config('app.scheme').'://')
                                    ->suffix(config('app.base_url'))
                                    ->maxLength(255),
                                TextInput::make('website')
                                    ->helperText('The tenant\'s website.')
                                    ->url()
                                    ->maxLength(255),
                            ]),
                        Tab::make('Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                DateTimePicker::make('last_login_at')
                                    ->label('Last Login')
                                    ->helperText('The last time a user logged into the tenant account.')
                                    ->nullable(),
                                DateTimePicker::make('setup_completed_at')
                                    ->label('Setup Completed')
                                    ->helperText('The time the account completed initial setup.')
                                    ->nullable(),
                            ]),
                        Tab::make('Billing')
                            ->visible(fn ($operation): bool => $operation !== 'create')
                            ->icon('heroicon-o-credit-card')
                            ->columns(2)
                            ->schema([
                                TextInput::make('invoice_emails')
                                    ->columnSpanFull()
                                    ->maxLength(255)
                                    ->label('Invoice Emails')
                                    ->helperText('Separate using a comma for multiple email addresses.')
                                    ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                                TextInput::make('billing_address')
                                    ->maxLength(255)
                                    ->label('Address')
                                    ->disabled(),
                                TextInput::make('billing_address_line_2')
                                    ->maxLength(255)
                                    ->label('Address Line 2')
                                    ->disabled(),
                                TextInput::make('billing_city')
                                    ->maxLength(255)
                                    ->label('City')
                                    ->disabled(),
                                TextInput::make('billing_state')
                                    ->maxLength(255)
                                    ->label('State')
                                    ->disabled(),
                                TextInput::make('billing_postal_code')
                                    ->maxLength(255)
                                    ->label('Postal Code')
                                    ->disabled(),
                                TextInput::make('billing_country')
                                    ->maxLength(255)
                                    ->label('Country')
                                    ->disabled(),
                                TextInput::make('vat_id')
                                    ->maxLength(255)
                                    ->label('VAT ID')
                                    ->disabled()
                                    ->columnSpanFull(),
                                DateTimePicker::make('trial_ends_at')
                                    ->label('Trial Ends At')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Database')
                            ->visible(fn ($operation): bool => $operation !== 'create')
                            ->icon('heroicon-o-circle-stack')
                            ->schema([
                                TextInput::make('tenancy_db_name')
                                    ->maxLength(255)
                                    ->label('Database Name')
                                    ->disabled(),
                            ]),
                        Tab::make('Payment')
                            ->visible(fn ($operation): bool => $operation !== 'create')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextInput::make('stripe_id')
                                    ->maxLength(255)
                                    ->label('Stripe ID')
                                    ->disabled(),
                                TextInput::make('pm_type')
                                    ->maxLength(255)
                                    ->label('Payment Method Type')
                                    ->disabled(),
                                TextInput::make('pm_last_four')
                                    ->maxLength(255)
                                    ->label('Payment Method Last Four')
                                    ->disabled(),
                                TextInput::make('pm_expiration')
                                    ->maxLength(255)
                                    ->label('Payment Method Expiration')
                                    ->disabled(),
                            ]),
                        Tab::make('Metadata')
                            ->visible(fn ($operation): bool => $operation !== 'create')
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
            ->emptyStateDescription('There are no tenants to display.')
            ->columns([
                TextColumn::make('id')
                    ->label(__('PERSCOM ID'))
                    ->sortable(),
                TextColumn::make('name')
                    ->copyable()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('url')
                    ->label(__('URL'))
                    ->copyable()
                    ->sortable()
                    ->openUrlInNewTab()
                    ->url(fn ($state) => $state),
                TextColumn::make('subscription_status')
                    ->label('Subscription Status')
                    ->badge(),
                TextColumn::make('term')
                    ->label('Subscription Term')
                    ->color('gray')
                    ->getStateUsing(fn (Tenant $record) => Str::headline($record->sparkPlan()?->interval ?? 'No Subscription'))
                    ->badge(),
                IconColumn::make('trial')
                    ->boolean()
                    ->getStateUsing(fn (Tenant $record) => $record->onTrial()),
                IconColumn::make('customer')
                    ->boolean()
                    ->getStateUsing(fn (Tenant $record) => $record->hasStripeId()),
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('login')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('gray')
                    ->modalDescription('Login to the tenant using the user below.')
                    ->visible(fn (Tenant $record) => $record->setup_completed)
                    ->schema([
                        Select::make('user')
                            ->searchable()
                            ->helperText('Select the user to login as.')
                            ->options(fn (Tenant $record) => $record->run(fn () => User::query()->orderBy('name')->whereHas('roles', function (Builder $query): void {
                                $query->where('name', Utils::getSuperAdminName());
                            })->get()->pluck('name', 'id')->toArray()))
                            ->required(),
                    ])
                    ->action(function (Action $action, Tenant $record, array $data) {
                        // @phpstan-ignore-next-line
                        $token = tenancy()->impersonate($record, data_get($data, 'user'), $record->url, 'web');

                        return redirect()->to($record->route('tenant.impersonation', [
                            'token' => $token,
                        ]));
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                Filter::make('subscription_status')
                    ->schema([
                        Select::make('subscription_status')
                            ->label('Subscription Status')
                            ->preload()
                            ->multiple()
                            ->options(SubscriptionStatus::class),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $query->when(filled(data_get($data, 'subscription_status')), function (Builder $query) use ($data): void {
                            $query->whereHas('subscriptions', fn (Builder $query) => $query->whereIn('stripe_status', Arr::wrap(data_get($data, 'subscription_status'))));
                        });
                    }),
                Filter::make('subscription_price')
                    ->schema([
                        TextInput::make('subscription_price')
                            ->label('Subscription Price'),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $query->when(filled(data_get($data, 'subscription_price')), function (Builder $query) use ($data): void {
                            $query->whereHas('subscriptions', fn (Builder $query) => $query->whereHas('items', fn (Builder $query) => $query->whereLike('stripe_price', '%'.data_get($data, 'subscription_price').'%')));
                        });
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    SwapSubscriptionBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            DomainsRelationManager::class,
            SubscriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
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
        $data = [
            'ID' => (string) $record->id,
            'Tenant' => $record->name,
            'Email' => $record->email,
        ];

        if ($record->url) {
            $data['URL'] = $record->url;
        }

        return $data;
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
                ->button()
                ->url(fn () => $record->url, shouldOpenInNewTab: true),
        ];
    }
}
