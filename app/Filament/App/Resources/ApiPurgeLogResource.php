<?php

declare(strict_types=1);

namespace App\Filament\App\Resources;

use App\Models\ApiPurgeLog;
use Filament\Panel;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ApiPurgeLogResource extends BaseResource
{
    protected static ?string $model = ApiPurgeLog::class;

    protected static ?string $label = 'Cache Purge Log';

    protected static bool $shouldRegisterNavigation = false;

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trace_id')
                    ->copyable()
                    ->label('Trace ID'),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Cache Tags')
                    ->badge()
                    ->listWithLineBreaks(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Success' => 'success',
                        'Failure' => 'danger',
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
