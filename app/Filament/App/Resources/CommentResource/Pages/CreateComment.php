<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CommentResource\Pages;

use App\Filament\App\Resources\CommentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;
}
