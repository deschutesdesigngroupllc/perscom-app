<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CombatRecordResource\Pages;

use App\Filament\App\Resources\CombatRecordResource;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCombatRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = CombatRecordResource::class;

    protected ?string $subheading = 'Keep track of a user\'s notable engagements during their time in the organization.';

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
