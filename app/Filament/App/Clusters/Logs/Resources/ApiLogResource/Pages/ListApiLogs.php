<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources\ApiLogResource\Pages;

use App\Filament\App\Clusters\Logs\Resources\ApiLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApiLogs extends ListRecords
{
    protected static string $resource = ApiLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
