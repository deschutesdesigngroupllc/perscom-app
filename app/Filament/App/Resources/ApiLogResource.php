<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApiLogResource\Pages\ListApiLogs;
use App\Filament\App\Resources\ApiLogResource\Pages\ViewApiLog;
use App\Filament\App\Resources\ApiLogResource\RelationManagers\PurgesRelationManager;
use App\Models\ApiLog;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Panel;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\Constraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Parallax\FilamentSyntaxEntry\SyntaxEntry;
use Symfony\Component\HttpFoundation\Response;
use UnitEnum;

class ApiLogResource extends BaseResource
{
    protected static ?string $model = ApiLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationParentItem = 'API Keys';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Logs';

    protected static ?string $label = 'API Logs';

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([
                Action::make('new')
                    ->label('New API key')
                    ->url(PassportTokenResource::getUrl('create')),
            ])
            ->emptyStateDescription('Create your first API key to start integrating with PERSCOM\'s powerful API.')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->select(['id', 'log_name', 'created_at', 'causer_id', 'causer_type', 'event']))
            ->columns([
                TextColumn::make('id')
                    ->copyable()
                    ->label('Log ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('request_id')
                    ->copyable()
                    ->label('Request ID')
                    ->searchable(query: fn (Builder|ApiLog $query, string $search) => $query->whereMeta('request_id', 'LIKE', "%$search%")->orWhereMeta('trace_id', 'like', "%$search%")->orWhereMeta('trace_id', 'like', "%$search%")),
                TextColumn::make('log_name')
                    ->label('Log')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'api' => 'API',
                        'oauth' => 'OAuth',
                        default => Str::title($state)
                    })
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('causer.name')
                    ->label('Author')
                    ->sortable(),
                TextColumn::make('method')
                    ->badge(),
                TextColumn::make('endpoint')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->color(fn ($state): string => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        default => 'danger',
                    })
                    ->badge()
                    ->suffix(fn ($state): string => ' '.Response::$statusTexts[(int) $state]),
                TextColumn::make('duration')
                    ->default('0')
                    ->numeric()
                    ->suffix(' ms'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(false)
                    ->sortable()
                    ->label('Requested'),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->filters([
                SelectFilter::make('author')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            filled(data_get($data, 'values')),
                            function (Builder $query) use ($data): void {
                                $query->where(fn (Builder $query) => collect(data_get($data, 'values'))->each(fn ($id) => $query->orWhereMorphRelation('causer', User::class, 'causer_id', '=', $id)));
                            },
                        )),
                SelectFilter::make('log_name')
                    ->label('Log')
                    ->options([
                        'api' => 'API',
                        'oauth' => 'OAuth',
                    ]),
                SelectFilter::make('method')
                    ->multiple()
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->query(fn (Builder|ApiLog $query, array $data): Builder => $query
                        ->when(
                            filled(data_get($data, 'values')),
                            function (Builder|ApiLog $query) use ($data): void {
                                $query->where(fn (Builder|ApiLog $query) => collect(data_get($data, 'values'))->each(fn ($method) => $query->orWhereMeta('method', $method)));
                            },
                        )),
                QueryBuilder::make()
                    ->constraints([
                        Constraint::make('request_id')
                            ->label('Request ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder|ApiLog $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->whereMeta('request_id', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder|ApiLog $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->whereMeta('request_id', 'LIKE', "%$text%");
                                                }
                                            );
                                    }),
                            ]),
                        Constraint::make('trace_id')
                            ->label('Trace ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder|ApiLog $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->whereMeta('trace_id', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder|ApiLog $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->whereMeta('trace_id', 'LIKE', "%$text%");
                                                }
                                            );
                                    }),
                            ]),
                        Constraint::make('endpoint')
                            ->icon('heroicon-o-globe-alt')
                            ->operators([
                                ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder|ApiLog $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->whereMeta('endpoint', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder|ApiLog $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->whereMeta('endpoint', 'LIKE', "%$text%");
                                                }
                                            );
                                    }),
                            ]),
                        DateConstraint::make('created_at')
                            ->label('Requested'),
                        Constraint::make('status')
                            ->icon('heroicon-o-flag')
                            ->operators([
                                ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder|ApiLog $query) use ($settings): void {
                                                        $text = data_get($settings, 'text');
                                                        $query->whereMeta('status', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                        return $query
                                            ->when(filled(data_get($settings, 'text')),
                                                function (Builder|ApiLog $query) use ($settings): void {
                                                    $text = data_get($settings, 'text');
                                                    $query->whereMeta('status', 'LIKE', "%$text%");
                                                }
                                            );

                                    }),
                            ]),
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Client')
                        ->icon('heroicon-o-user')
                        ->schema([
                            TextEntry::make('request_id')
                                ->copyable()
                                ->label('Request ID'),
                            TextEntry::make('trace_id')
                                ->copyable()
                                ->label('Trace ID'),
                            TextEntry::make('causer.name')
                                ->label('Author'),
                            TextEntry::make('duration')
                                ->default('0')
                                ->numeric()
                                ->suffix(' ms'),
                            TextEntry::make('created_at')
                                ->label('Requested'),
                            TextEntry::make('ip_address')
                                ->label('IP Address'),
                        ]),
                    Tab::make('Request')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->schema([
                            TextEntry::make('method')
                                ->badge(),
                            TextEntry::make('endpoint')
                                ->badge()
                                ->color('gray'),
                            KeyValueEntry::make('request_headers')
                                ->label('Headers')
                                ->keyLabel('Header')
                                ->valueLabel('Value')
                                ->helperText('Sensitive headers are hidden by default.')
                                ->getStateUsing(fn (?ApiLog $record) => collect($record->request_headers)
                                    ->reject(fn ($value, $key): bool => in_array(Str::lower($key), [
                                        'authorization',
                                        '_token',
                                        'x-csrf-token',
                                        'x-xsrf-token',
                                    ]))
                                    ->mapWithKeys(fn ($value, $header) => [$header => collect($value)->map(fn ($value) => Str::limit($value))->join(', ')])->toArray()
                                ),
                            SyntaxEntry::make('body')
                                ->language('json'),
                        ]),
                    Tab::make('Response')
                        ->icon('heroicon-o-cloud-arrow-down')
                        ->schema([
                            TextEntry::make('status')
                                ->suffix(fn ($state): string => ' '.Response::$statusTexts[(int) $state])
                                ->color(fn ($state): string => match (true) {
                                    $state >= 200 && $state < 300 => 'success',
                                    default => 'danger',
                                })
                                ->badge(),
                            KeyValueEntry::make('request_headers')
                                ->label('Headers')
                                ->keyLabel('Header')
                                ->valueLabel('Value')
                                ->getStateUsing(fn (?ApiLog $record) => collect($record->response_headers)->mapWithKeys(fn ($value, $header) => [$header => collect($value)->join(', ')])->toArray()),
                            SyntaxEntry::make('content')
                                ->language('json'),
                        ]),
                ]),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            PurgesRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiLogs::route('/'),
            'view' => ViewApiLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    /**
     * @param  ApiLog  $record
     */
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'ID' => $record->id,
            'Endpoint' => $record->endpoint,
            'Method' => $record->method,
            'Status' => $record->status,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return array_merge(parent::getGloballySearchableAttributes(), [
            'request_id',
            'trace_id',
        ]);
    }

    protected static function applyGlobalSearchAttributeConstraint(Builder $query, string $search, array $searchAttributes, bool &$isFirst): Builder
    {
        $originalSearchAttributes = $searchAttributes;

        $searchAttributes = array_filter(Arr::pluck($searchAttributes, [
            'request_id',
            'trace_id',
        ]));

        /** @var Builder|ApiLog $query */
        $query = parent::applyGlobalSearchAttributeConstraint($query, $search, $searchAttributes, $isFirst);

        if (in_array('request_id', $originalSearchAttributes)) {
            $query->orWhereMeta('request_id', $search);
        }

        if (in_array('trace_id', $originalSearchAttributes)) {
            $query->orWhereMeta('trace_id', $search);
        }

        return $query;
    }
}
