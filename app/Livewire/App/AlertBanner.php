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
        $this->alerts = Alert::query()->orderBy('order', 'desc')->whereJsonContains('channels', 'dashboard')->get()->map(function (Alert $alert) {
            return [
                'title' => $alert->title,
                'message' => $alert->message,
                'url' => $alert->url,
                'link' => $alert->link_text,
            ];
        })->toArray();
    }

    public function render(): View
    {
        return view('livewire.app.alert-banner');
    }
}
