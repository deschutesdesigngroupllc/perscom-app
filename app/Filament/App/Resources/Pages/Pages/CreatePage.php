<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Pages\Pages;

use App\Filament\App\Resources\Pages\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;
}
