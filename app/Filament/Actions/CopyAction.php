<?php

declare(strict_types=1);

namespace App\Filament\Actions;

use Closure;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;
use JsonException;

class CopyAction extends Action
{
    protected Closure|string|null $copyable = null;

    public function setUp(): void
    {
        parent::setUp();

        $this
            ->dispatch('FilamentCopyActions')
            ->successNotificationTitle(__('Copied!'))
            ->icon('heroicon-o-clipboard-document')
            ->extraAttributes(fn (): array => [
                'x-data' => '',
                'x-on:click' => new HtmlString(
                    'window.navigator.clipboard.writeText('.$this->getCopyable().');'
                    .((($title = $this->getSuccessNotificationTitle()) !== null && ($title = $this->getSuccessNotificationTitle()) !== '' && ($title = $this->getSuccessNotificationTitle()) !== '0') ? ' $tooltip('.Js::from($title).');' : '')
                ),
            ]);
    }

    public static function getDefaultName(): ?string
    {
        return 'copy';
    }

    public function action(Closure|string|null $action): static
    {
        $this->dispatch(null);

        return parent::action($action);
    }

    public function copyable(Closure|string|null $copyable): self
    {
        $this->copyable = $copyable;

        return $this;
    }

    /**
     * @throws JsonException
     */
    public function getCopyable(): Js
    {
        return Js::from($this->evaluate($this->copyable));
    }
}
