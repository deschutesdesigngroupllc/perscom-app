<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources\WebhookLogResource\Pages;

use App\Filament\App\Clusters\Logs\Resources\WebhookLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWebhookLogs extends ListRecords
{
    protected static string $resource = WebhookLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
