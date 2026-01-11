<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\DocumentResource\Pages;

use App\Filament\App\Resources\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        /** @phpstan-ignore-next-line property.notFound */
        return Str::of($this->getRecord()?->description)
            ->limit()
            ->wrap('<div class="fi-header-subheading">', '</div>')
            ->toHtmlString();
    }

    /**
     * @return DeleteAction[]
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
