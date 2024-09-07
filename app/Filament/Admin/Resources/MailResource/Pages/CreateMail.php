<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MailResource\Pages;

use App\Filament\Admin\Resources\MailResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMail extends CreateRecord
{
    protected static string $resource = MailResource::class;
}
