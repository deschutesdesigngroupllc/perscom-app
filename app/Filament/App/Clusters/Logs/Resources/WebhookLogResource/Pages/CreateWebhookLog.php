<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Logs\Resources\WebhookLogResource\Pages;

use App\Filament\App\Clusters\Logs\Resources\WebhookLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWebhookLog extends CreateRecord
{
    protected static string $resource = WebhookLogResource::class;
}
