<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use App\Traits\Filament\CanCreateBatchRecords;
use App\Traits\Filament\ConfiguresModelNotifications;
use App\Traits\Filament\SetsUserFromRequestQuery;
use Filament\Resources\Pages\CreateRecord;

class CreateQualificationRecord extends CreateRecord
{
    use CanCreateBatchRecords;
    use ConfiguresModelNotifications;
    use SetsUserFromRequestQuery;

    protected static string $resource = QualificationRecordResource::class;
}
