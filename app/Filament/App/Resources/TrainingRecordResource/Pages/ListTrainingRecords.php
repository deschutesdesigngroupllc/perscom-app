<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TrainingRecordResource\Pages;

use App\Filament\App\Resources\TrainingRecordResource;
use App\Filament\App\Resources\TrainingRecordResource\Widgets\TrainingRecordStatsOverview;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainingRecords extends ListRecords
{
    use AdvancedTables;

    protected static string $resource = TrainingRecordResource::class;

    protected ?string $subheading = "Comprehensive records that encapsulate details of a training event, including the competencies acquired, the issuer of the credential, and the credential itself, providing a complete history of an individual's training experience.";

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
            TrainingRecordStatsOverview::class,
        ];
    }
}
