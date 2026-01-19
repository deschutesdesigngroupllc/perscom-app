<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use App\Filament\App\Resources\CombatRecordResource\Widgets\CombatRecordStatsOverview;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCombatRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = CombatRecordResource::class;

    protected ?string $subheading = 'Document notable engagements and operational actions.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CombatRecordStatsOverview::class,
        ];
    }
}
