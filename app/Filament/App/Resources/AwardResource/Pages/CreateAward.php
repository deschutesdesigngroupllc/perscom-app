<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AwardResource\Pages;

use App\Filament\App\Resources\AwardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAward extends CreateRecord
{
    protected static string $resource = AwardResource::class;
}
