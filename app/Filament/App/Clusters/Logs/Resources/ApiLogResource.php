<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources;

use App\Features\ApiAccessFeature;
use App\Filament\App\Clusters\Logs;
use App\Filament\App\Clusters\Logs\Resources\ApiLogResource\Pages;
use App\Filament\App\Resources\BaseResource;
use App\Filament\App\Resources\PassportTokenResource;
use App\Models\ApiLog;
use App\Models\User;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Laravel\Pennant\Feature;
use Parallax\FilamentSyntaxEntry\SyntaxEntry;
use Symfony\Component\HttpFoundation\Response;

class ApiLogResource extends BaseResource
{
    protected static ?string $model = ApiLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $cluster = Logs::class;

    protected static ?string $label = 'API Logs';

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateActions([
                Tables\Actions\Action::make('new')
                    ->label('New API key')
                    ->url(PassportTokenResource::getUrl('create')),
            ])
            ->emptyStateDescription('Create your first API key to start integrating with PERSCOM\'s powerful API.')
            ->columns([
                Tables\Columns\TextColumn::make('request_id')
                    ->copyable()
                    ->label('Request ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Log')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'api' => 'API',
                        'oauth' => 'OAuth',
                        default => Str::title($state)
                    })
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Author')
                    ->sortable(),
                Tables\Columns\TextColumn::make('method')
                    ->badge(),
                Tables\Columns\TextColumn::make('endpoint')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->color(fn ($state) => match (true) {
                        $state >= 200 && $state < 300 => 'success',
                        default => 'danger',
                    })
                    ->badge()
                    ->suffix(fn ($state) => ' '.Response::$statusTexts[(int) $state]),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable()
                    ->label('Requested'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('author')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->options(User::orderBy('name')->pluck('name', 'id'))
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled(data_get($data, 'values')),
                                function (Builder $query) use ($data) {
                                    // @phpstan-ignore-next-line
                                    $query->where(fn (Builder $query) => collect(data_get($data, 'values'))->each(fn ($id) => $query->orWhereMorphRelation('causer', User::class, 'causer_id', '=', $id)));
                                },
                            );
                    }),
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Log')
                    ->options([
                        'api' => 'API',
                        'oauth' => 'OAuth',
                    ]),
                Tables\Filters\SelectFilter::make('method')
                    ->multiple()
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled(data_get($data, 'values')),
                                function (Builder $query) use ($data) {
                                    // @phpstan-ignore-next-line
                                    $query->where(fn (Builder $query) => collect(data_get($data, 'values'))->each(fn ($method) => $query->orWhereJsonContains('properties->method', $method)));
                                },
                            );
                    }),
                Tables\Filters\QueryBuilder::make()
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\Constraint::make('request_id')
                            ->label('Request ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->request_id', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        } else {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->request_id', 'LIKE', "%$text%");
                                                    }
                                                );
                                        }
                                    }),
                            ]),
                        Tables\Filters\QueryBuilder\Constraints\Constraint::make('trace_id')
                            ->label('Trace ID')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->operators([
                                Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->trace_id', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        } else {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->trace_id', 'LIKE', "%$text%");
                                                    }
                                                );
                                        }
                                    }),
                            ]),
                        Tables\Filters\QueryBuilder\Constraints\Constraint::make('endpoint')
                            ->icon('heroicon-o-globe-alt')
                            ->operators([
                                Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->endpoint', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        } else {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->endpoint', 'LIKE', "%$text%");
                                                    }
                                                );
                                        }
                                    }),
                            ]),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label('Requested'),
                        Tables\Filters\QueryBuilder\Constraints\Constraint::make('status')
                            ->icon('heroicon-o-flag')
                            ->operators([
                                Tables\Filters\QueryBuilder\Constraints\TextConstraint\Operators\ContainsOperator::make()
                                    ->modifyBaseQueryUsing(function (Builder $query, array $settings, bool $isInverse) {
                                        if ($isInverse) {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->status', 'NOT LIKE', "%$text%");
                                                    }
                                                );
                                        } else {
                                            return $query
                                                ->when(filled(data_get($settings, 'text')),
                                                    function (Builder $query) use ($settings) {
                                                        $text = data_get($settings, 'text');
                                                        $query->where('properties->status', 'LIKE', "%$text%");
                                                    }
                                                );
                                        }

                                    }),
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
                            TextEntry::make('created_at')
                                ->label('Requested'),
                            TextEntry::make('ip_address')
                                ->label('IP Address'),
                        ]),
                    Tabs\Tab::make('Request')
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
                                    ->reject(fn ($value, $key) => in_array(Str::lower($key), [
                                        'authorization',
                                        '_token',
                                        'x-csrf-token',
                                        'x-xsrf-token',
                                    ]))
                                    ->mapWithKeys(fn ($value, $header) => [$header => collect($value)->map(fn ($value) => Str::limit($value))->join(', ')])->toArray()
                                ),
                            SyntaxEntry::make('body')
                                ->language('json'),
                            RepeatableEntry::make('files')
                                ->visible(fn (?ApiLog $record) => filled($record->files))
                                ->schema([
                                    TextEntry::make('name'),
                                    TextEntry::make('size')
                                        ->formatStateUsing(fn ($state) => Number::fileSize($state ?? 0)),
                                ]),
                        ]),
                    Tabs\Tab::make('Response')
                        ->icon('heroicon-o-cloud-arrow-down')
                        ->schema([
                            TextEntry::make('status')
                                ->suffix(fn ($state) => ' '.Response::$statusTexts[(int) $state])
                                ->color(fn ($state) => match (true) {
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiLogs::route('/'),
            'view' => Pages\ViewApiLog::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return parent::canAccess() && Feature::active(ApiAccessFeature::class);
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
}
