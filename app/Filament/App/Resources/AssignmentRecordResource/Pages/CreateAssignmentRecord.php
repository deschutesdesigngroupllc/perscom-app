<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AssignmentRecordResource\Pages;

use App\Filament\App\Resources\AssignmentRecordResource;
use App\Traits\Filament\CanCreateBatchRecords;
use App\Traits\Filament\ConfiguresModelNotifications;
use App\Traits\Filament\SavesAndHydratesCustomFieldData;
use App\Traits\Filament\SetsUserFromRequestQuery;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateAssignmentRecord extends CreateRecord
{
    use CanCreateBatchRecords;
    use ConfiguresModelNotifications;
    use SavesAndHydratesCustomFieldData;
    use SetsUserFromRequestQuery;

    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = AssignmentRecordResource::class;
}
