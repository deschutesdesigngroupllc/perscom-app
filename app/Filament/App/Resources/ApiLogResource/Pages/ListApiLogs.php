<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiLogResource\Pages;

use App\Filament\App\Resources\ApiLogResource;
use Filament\Resources\Pages\ListRecords;

class ListApiLogs extends ListRecords
{
    protected static string $resource = ApiLogResource::class;

    protected ?string $subheading = 'View recent API request activity and responses.';
}
