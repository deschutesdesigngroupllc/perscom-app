<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\Pages\Actions;

use App\Models\Page;
use Closure;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Override;

class CodeEditorAction extends Action
{
    protected Closure|Page|null $page = null;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Open Code Editor');
        $this->icon(Heroicon::OutlinedCodeBracket);
        $this->color('gray');
        $this->url(fn (): string => route('app.admin.pages.index', [
            'page' => $this->getPage(),
        ]), shouldOpenInNewTab: true);
    }

    public static function getDefaultName(): ?string
    {
        return 'code_editor';
    }

    public function page(Closure|Page|null $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->evaluate($this->page);
    }
}
