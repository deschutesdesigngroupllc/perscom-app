<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Pages;

use App\Filament\App\Resources\UserResource;
use App\Filament\App\Resources\UserResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\FieldsRelationManager;
use App\Filament\App\Resources\UserResource\RelationManagers\RolesRelationManager;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Stringable;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getRelationManagers(): array
    {
        return [
            AttachmentsRelationManager::class,
            FieldsRelationManager::class,
            RolesRelationManager::class,
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        /** @phpstan-ignore-next-line property.notFound */
        return new Stringable()
            ->when($this->getRecord()->rank, fn (Stringable $str) => $str->append(' ')->append($this->getRecord()->rank->abbreviation)->append(' ')->append($this->getRecord()->rank->name))
            ->when($this->getRecord()->position, fn (Stringable $str) => $str->append(', ')->append($this->getRecord()->position->name))
            ->when($this->getRecord()->unit, fn (Stringable $str) => $str->append(', ')->append($this->getRecord()->unit->name))
            ->wrap('<div class="fi-header-subheading">', '</div>')
            ->toHtmlString();
    }

    /**
     * @return Action[]
     */
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
