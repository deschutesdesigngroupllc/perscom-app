<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiLogResource\RelationManagers;

use App\Filament\App\Resources\ApiPurgeLogResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class PurgesRelationManager extends RelationManager
{
    protected static string $relationship = 'purges';

    public function table(Table $table): Table
    {
        return ApiPurgeLogResource::table($table)
            ->searchable(false)
            ->heading('Cache Purge Logs')
            ->emptyStateHeading('No API cache purge requests')
            ->description('The cache purge requests associated with this API request.')
            ->emptyStateDescription('There are no API cache purges associated with this request.');
    }
}
