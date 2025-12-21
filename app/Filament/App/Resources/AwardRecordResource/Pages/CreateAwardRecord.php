<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardRecordResource\Pages;

use App\Filament\App\Resources\AwardRecordResource;
use App\Traits\Filament\CanCreateBatchRecords;
use App\Traits\Filament\ConfiguresModelNotifications;
use App\Traits\Filament\SetsUserFromRequestQuery;
use Filament\Resources\Pages\CreateRecord;

class CreateAwardRecord extends CreateRecord
{
    use CanCreateBatchRecords;
    use ConfiguresModelNotifications;
    use SetsUserFromRequestQuery;

    protected static string $resource = AwardRecordResource::class;
}
