<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class TorchlightCode extends Field
{
    public string $language = '';

    public string $theme = '';

    protected string $view = 'forms.components.torchlight-code';

    public function language(string $value): static
    {
        $this->language = $value;

        return $this;
    }

    public function theme(string $value): static
    {
        $this->theme = $value;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }
}
