<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use App\Traits\Filament\HandlesBatchCreatingRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateQualificationRecord extends CreateRecord
{
    use HandlesBatchCreatingRecords;

    protected static string $resource = QualificationRecordResource::class;
}
