<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCombatRecords extends ListRecords
{
    protected static string $resource = CombatRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CombatRecordResource\Widgets\CombatRecordStatsOverview::class,
        ];
    }
}
