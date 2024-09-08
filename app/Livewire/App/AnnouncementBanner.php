<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Models\Announcement;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AnnouncementBanner extends Component
{
    public ?array $announcements;

    public function mount(): void
    {
        $this->announcements = Announcement::global()->get()->map(function (Announcement $announcement) {
            return [
                'title' => $announcement->title,
                'content' => $announcement->content,
                'color' => $announcement->color,
            ];
        })->toArray();
    }

    public function render(): View
    {
        return view('livewire.app.announcement-banner');
    }
}
