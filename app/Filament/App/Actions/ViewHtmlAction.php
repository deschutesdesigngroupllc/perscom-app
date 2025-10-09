<?php

declare(strict_types=1);

namespace App\Filament\App\Actions;

use Closure;
use Filament\Actions\Action;

class ViewHtmlAction extends Action
{
    protected Closure|string|null $html = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (): bool => $this->getHtml() !== null);
        $this->modalSubmitAction(false);
        $this->modalCancelActionLabel('Close');
        $this->modalContent(fn () => view('filament.app.view-html', [
            'html' => $this->getHtml(),
        ]));
    }

    public static function getDefaultName(): ?string
    {
        return 'view_html';
    }

    public function html(Closure|string|null $html): static
    {
        $this->html = $html;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->evaluate($this->html);
    }
}
