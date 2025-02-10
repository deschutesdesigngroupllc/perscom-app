<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationRecordResource\Pages;

use App\Filament\App\Resources\QualificationRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQualificationRecords extends ListRecords
{
    protected static string $resource = QualificationRecordResource::class;

    protected ?string $subheading = 'Keep track of a users capabilities, qualifications, training and certifications.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            QualificationRecordResource\Widgets\QualificationRecordStatsOverview::class,
        ];
    }
}
