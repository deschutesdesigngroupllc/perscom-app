<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Models\Document;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DocumentTags extends Component
{
    public function render(): View
    {
        return view('livewire.app.document-tags', [
            'tags' => Document::$availableTags,
        ]);
    }
}
