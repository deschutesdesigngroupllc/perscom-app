<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Traits\Filament\CanCreateBatchRecords;
use App\Traits\Filament\ConfiguresModelNotifications;
use App\Traits\Filament\SetsUserFromRequestQuery;
use Filament\Resources\Pages\CreateRecord;

class CreateAssignmentRecord extends CreateRecord
{
    use CanCreateBatchRecords;
    use ConfiguresModelNotifications;
    use SetsUserFromRequestQuery;

    protected static string $resource = AssignmentRecordResource::class;
}
