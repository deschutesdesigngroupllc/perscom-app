<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FieldResource\Pages;

use App\Filament\App\Resources\FieldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFields extends ListRecords
{
    protected static string $resource = FieldResource::class;

    protected ?string $subheading = 'Fields provide advanced customization to several PERSCOM features.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
