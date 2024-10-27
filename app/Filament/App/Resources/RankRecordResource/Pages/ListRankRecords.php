<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\RankRecordResource\Pages;

use App\Filament\App\Resources\RankRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRankRecords extends ListRecords
{
    protected static string $resource = RankRecordResource::class;

    protected ?string $subheading = 'Keep track of promotions, demotions and lateral transfers across the organization.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RankRecordResource\Widgets\RankRecordStatsOverview::class,
        ];
    }
}
