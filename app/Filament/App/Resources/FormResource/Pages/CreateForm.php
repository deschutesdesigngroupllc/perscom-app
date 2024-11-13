<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Filament\App\Resources\FormResource;
use App\Traits\Filament\InteractsWithModelNotifications;
use Filament\Resources\Pages\CreateRecord;

class CreateForm extends CreateRecord
{
    use InteractsWithModelNotifications;

    protected static string $resource = FormResource::class;
}
