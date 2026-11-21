<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\TaskResource\Pages;

use App\Filament\App\Resources\TaskResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateTask extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = TaskResource::class;
}
