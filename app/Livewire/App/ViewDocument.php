<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Models\Document;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ViewDocument extends Component
{
    public ?Document $document;

    public ?User $user;

    public mixed $model;

    public function mount(?Document $document, ?User $user, mixed $model): void
    {
        $this->document = $document;
        $this->user = $user;
        $this->model = $model;
    }

    public function render(): View
    {
        return view('livewire.app.view-document');
    }
}
