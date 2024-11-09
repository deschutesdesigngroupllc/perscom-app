<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\Pages;

use App\Filament\App\Resources\RankRecordResource;
use App\Traits\Filament\HandlesBatchCreatingRecords;
use Filament\Resources\Pages\CreateRecord;

class CreateRankRecord extends CreateRecord
{
    use HandlesBatchCreatingRecords;

    protected static string $resource = RankRecordResource::class;
}
