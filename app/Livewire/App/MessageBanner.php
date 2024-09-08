<?php

declare(strict_types=1);

namespace App\Livewire\App;

use App\Models\Message;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MessageBanner extends Component
{
    public ?array $messages;

    public function mount(): void
    {
        $this->messages = Message::all()->map(function (Message $message) {
            return [
                'title' => $message->title,
                'message' => $message->message,
            ];
        })->toArray();
    }

    public function render(): View
    {
        return view('livewire.app.message-banner');
    }
}
