<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ApiLogResource\Pages\ViewApiLog;
use App\Filament\App\Resources\ApiPurgeLogResource\Pages\ListApiPurgeLogs;
use App\Models\ApiPurgeLog;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use UnitEnum;

class ApiPurgeLogResource extends BaseResource
{
    protected static ?string $model = ApiPurgeLog::class;

    protected static ?string $navigationLabel = 'Purges';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationParentItem = 'API Keys';

    protected static string|UnitEnum|null $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 9;

    protected static ?string $label = 'API cache logs';

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateDescription('There are no API cache logs to view.')
            ->columns([
                TextColumn::make('apiLog.id')
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
                TextColumn::make('trace_id')
                    ->copyable()
                    ->searchable(['properties'])
                    ->label('Trace ID'),
                TextColumn::make('description')
                    ->placeholder('No Description')
                    ->label('Event')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => Str::title($state))
                    ->sortable(),
                TextColumn::make('tags')
                    ->label('Cache Tags')
                    ->badge()
                    ->listWithLineBreaks(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Success' => 'success',
                        default => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->toggleable(false)
                    ->sortable()
                    ->dateTime()
                    ->since()
                    ->label('Purged'),
            ]);
    }

    /**
     * @return PageRegistration[]
     */
    public static function getPages(): array
    {
        return [
            'index' => ListApiPurgeLogs::route('/'),
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
