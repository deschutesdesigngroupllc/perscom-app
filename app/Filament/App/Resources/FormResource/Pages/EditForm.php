<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Pages;

use App\Filament\App\Resources\FormResource;
use App\Traits\Filament\ConfiguresModelNotifications;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

class EditForm extends EditRecord
{
    use ConfiguresModelNotifications;

    protected static string $resource = FormResource::class;

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
