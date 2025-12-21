<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use App\Traits\Filament\CanCreateBatchRecords;
use App\Traits\Filament\ConfiguresModelNotifications;
use App\Traits\Filament\SetsUserFromRequestQuery;
use Filament\Resources\Pages\CreateRecord;

class CreateCombatRecord extends CreateRecord
{
    use CanCreateBatchRecords;
    use ConfiguresModelNotifications;
    use SetsUserFromRequestQuery;

    protected static string $resource = CombatRecordResource::class;
}
