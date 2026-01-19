<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ServiceRecordResource\Pages;

use App\Filament\App\Resources\ServiceRecordResource;
use App\Filament\App\Resources\ServiceRecordResource\Widgets\ServiceRecordStatsOverview;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = ServiceRecordResource::class;

    protected ?string $subheading = 'Maintain general service entries and administrative notes.';

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
            ServiceRecordStatsOverview::class,
        ];
    }
}
