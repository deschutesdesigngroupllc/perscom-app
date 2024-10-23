<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Models\Alert;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AlertBanner extends Component
{
    public ?array $alerts;

    public function mount(): void
    {
        $this->alerts = Alert::all()->map(function (Alert $alert) {
            return [
                'title' => $alert->title,
                'message' => $alert->message,
            ];
        })->toArray();
    }

    public function render(): View
    {
        return view('livewire.app.alert-banner');
    }
}
