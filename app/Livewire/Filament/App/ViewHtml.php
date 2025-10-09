<?php

declare(strict_types=1);

namespace App\Livewire\Filament\App;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ViewHtml extends Component
{
    public ?string $html = null;

    public function mount(?string $html = null): void
    {
        $this->html = $html;
    }

    public function render(): View
    {
        return view('livewire.filament.app.view-html');
    }
}
