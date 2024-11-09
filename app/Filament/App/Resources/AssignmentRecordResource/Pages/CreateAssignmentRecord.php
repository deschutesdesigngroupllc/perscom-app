<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Traits\Filament\HandlesBatchCreatingRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateAssignmentRecord extends CreateRecord
{
    use HandlesBatchCreatingRecords;

    protected static string $resource = AssignmentRecordResource::class;
}
