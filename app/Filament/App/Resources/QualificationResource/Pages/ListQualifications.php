<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\QualificationResource\Pages;

use App\Filament\App\Resources\QualificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQualifications extends ListRecords
{
    protected static string $resource = QualificationResource::class;

    protected ?string $subheading = 'Qualifications establish what a user is allowed to do based on performance.';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
