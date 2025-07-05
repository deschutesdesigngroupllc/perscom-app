<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiPurgeLogResource\Pages;

use App\Filament\App\Resources\ApiPurgeLogResource;
use Filament\Resources\Pages\ListRecords;

class ListApiPurgeLogs extends ListRecords
{
    protected static string $resource = ApiPurgeLogResource::class;

    protected ?string $subheading = 'Your most recent API purge requests.';
}
