<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Filament\App\Resources\FormResource;
use App\Traits\Filament\ConfiguresModelNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    use ConfiguresModelNotifications;

    protected static string $resource = FormResource::class;
}
