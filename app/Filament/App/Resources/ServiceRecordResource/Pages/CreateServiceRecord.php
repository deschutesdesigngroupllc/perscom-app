<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Traits\Filament\HandlesBatchCreatingRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceRecord extends CreateRecord
{
    use HandlesBatchCreatingRecords;

    protected static string $resource = ServiceRecordResource::class;
}
