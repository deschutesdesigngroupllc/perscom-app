<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookLogResource\Pages;

use App\Filament\App\Resources\WebhookLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWebhookLogs extends ListRecords
{
    protected static string $resource = WebhookLogResource::class;

    protected ?string $subheading = 'Your most recent webhook requests.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
