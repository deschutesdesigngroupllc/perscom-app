<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\FormResource\Actions;

use App\Models\Form;
use Filament\Actions\Action;

class PublicUrlAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Public URL');
        $this->color('gray');
        $this->tooltip(fn (Form $record): string => route('filament.public.pages.forms.{slug}', ['slug' => $record->slug]));
        $this->openUrlInNewTab();
        $this->icon('heroicon-o-link');
        $this->visible(fn (Form $record) => $record->is_public);
        $this->action(fn (Form $record) => $this->redirect(route('filament.public.pages.forms.{slug}', ['slug' => $record->slug])));
    }

    public static function getDefaultName(): ?string
    {
        return 'public_url';
    }
}
