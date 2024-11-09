<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use App\Traits\Filament\HandlesBatchCreatingRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateAwardRecord extends CreateRecord
{
    use HandlesBatchCreatingRecords;

    protected static string $resource = AwardRecordResource::class;
}
