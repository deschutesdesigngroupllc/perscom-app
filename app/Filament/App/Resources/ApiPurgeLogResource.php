<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApiLogResource\Pages\ViewApiLog;
use App\Models\ApiPurgeLog;
use Filament\Panel;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiPurgeLogResource extends BaseResource
{
    protected static ?string $model = ApiPurgeLog::class;

    protected static ?string $navigationLabel = 'Purges';

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationParentItem = 'API Keys';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 6;

    protected static ?string $label = 'API Cache Purge Log';

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('apiLog.id')
                    ->label('API Log ID')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition(IconPosition::After)
                    ->url(function (ApiPurgeLog $record): ?string {
                        if (blank($apiLogs = $record->apiLog)) {
                            return null;
                        }

                        return ViewApiLog::getUrl([
                            'record' => $apiLogs->first(),
                        ]);
                    }, shouldOpenInNewTab: true),
                Tables\Columns\TextColumn::make('trace_id')
                    ->copyable()
                    ->searchable(['properties'])
                    ->label('Trace ID'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Event')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Str::title($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Cache Tags')
                    ->badge()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Success' => 'success',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->label('Purged'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ApiPurgeLogResource\Pages\ListApiPurgeLogs::route('/'),
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
}
