<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationLogResource\Pages;

use App\Filament\App\Resources\AutomationLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAutomationLogs extends ListRecords
{
    protected static string $resource = AutomationLogResource::class;

    protected ?string $subheading = 'Your most recent automation executions.';
}
