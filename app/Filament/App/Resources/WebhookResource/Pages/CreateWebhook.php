<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookResource\Pages;

use App\Filament\App\Resources\WebhookResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateWebhook extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = WebhookResource::class;
}
