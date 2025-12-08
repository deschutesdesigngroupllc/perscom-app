<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Categories\Pages;

use App\Filament\App\Resources\Categories\CategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected ?string $subheading = 'Categories provide a way to logically group similar resources.';

    /**
     * @return CreateAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
