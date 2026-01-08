<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use App\Rules\TurnstileRule;
use Filament\Forms\Components\Field;

class Turnstile extends Field
{
    protected string $view = 'filament.forms.components.turnstile';

    protected string $language = 'en-US';

    protected string $theme = 'auto';

    protected string $size = 'normal';

    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel();
        $this->validationAttribute('CAPTCHA');
        $this->dehydrated(false);
        $this->rules(['required', new TurnstileRule]);
        $this->hidden(fn (): bool => blank(config('services.cloudflare.turnstile.site_key')));
    }

    public function language(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function theme(string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function size(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->evaluate($this->language);
    }

    public function getTheme(): string
    {
        return $this->evaluate($this->theme);
    }

    public function getSize(): string
    {
        return $this->evaluate($this->size);
    }
}
