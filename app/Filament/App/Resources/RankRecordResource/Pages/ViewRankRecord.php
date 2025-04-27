<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\Pages;

use App\Filament\App\Resources\RankRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRankRecord extends ViewRecord
{
    protected static string $resource = RankRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
