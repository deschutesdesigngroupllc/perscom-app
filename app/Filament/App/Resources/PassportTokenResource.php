<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PassportTokenResource\Pages\CreatePassportToken;
use App\Filament\App\Resources\PassportTokenResource\Pages\EditPassportToken;
use App\Filament\App\Resources\PassportTokenResource\Pages\ListPassportTokens;
use App\Filament\App\Resources\PassportTokenResource\Pages\ViewPassportToken;
use App\Models\PassportClient;
use App\Models\PassportToken;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Passport\Passport;
use UnitEnum;

class PassportTokenResource extends BaseResource
{
    protected static ?string $model = PassportToken::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 5;

    protected static ?string $pluralModelLabel = 'API keys';

    protected static ?string $modelLabel = 'API key';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->helperText('An identifying name for the API key')
                    ->required()
                    ->maxLength(255),
                Select::make('scopes')
                    ->helperText(fn ($operation): string => match ($operation) {
                        'edit' => 'Please create a new API key to change the scopes.',
                        default => 'The scopes that the API key will have access to.'
                    })
                    ->multiple()
                    ->live()
                    ->disabled(fn ($operation): bool => $operation !== 'create')
                    ->options(fn () => Passport::scopes()->pluck('id', 'id')->sort())
                    ->hidden(fn (Get $get): mixed => $get('all_scopes')),
                Checkbox::make('all_scopes')
                    ->visibleOn('create')
                    ->default(true)
                    ->live()
                    ->inline()
                    ->helperText('Select to allow access to all scopes.'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('scopes')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList()
                    ->expandableLimitedList()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable(),
            ])
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
        return parent::getEloquentQuery()
            ->whereDoesntHave('client', fn (Builder $query) => $query->where('name', '<>', PassportClient::SYSTEM_PERSONAL_ACCESS_CLIENT));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPassportTokens::route('/'),
            'create' => CreatePassportToken::route('/create'),
            'view' => ViewPassportToken::route('/{record}'),
            'edit' => EditPassportToken::route('/{record}/edit'),
        ];
    }
}
