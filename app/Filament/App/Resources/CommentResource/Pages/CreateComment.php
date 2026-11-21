<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CommentResource\Pages;

use App\Filament\App\Resources\CommentResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateComment extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = CommentResource::class;
}
