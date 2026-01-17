<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationResource\Pages;

use App\Filament\App\Resources\AutomationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAutomation extends CreateRecord
{
    protected static string $resource = AutomationResource::class;
}
