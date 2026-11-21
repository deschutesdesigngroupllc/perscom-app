<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\SubmissionResource\Pages;

use App\Filament\App\Resources\SubmissionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSubmission extends CreateRecord
{
    protected Width|string|null $maxContentWidth = Width::SevenExtraLarge;

    protected static string $resource = SubmissionResource::class;
}
