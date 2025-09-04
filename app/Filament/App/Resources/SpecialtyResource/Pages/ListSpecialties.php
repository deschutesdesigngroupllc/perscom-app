<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SpecialtyResource\Pages;

use App\Filament\App\Resources\SpecialtyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpecialties extends ListRecords
{
    protected static string $resource = SpecialtyResource::class;

    protected ?string $subheading = 'Specialities help to further delineate personnel responsibilities and capabilities.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
