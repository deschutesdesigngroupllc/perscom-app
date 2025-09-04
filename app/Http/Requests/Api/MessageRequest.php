<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Enums\NotificationChannel;
use Illuminate\Validation\Rule;
use Orion\Http\Requests\Request;

class MessageRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'message' => 'string|max:65535',
            'channels' => Rule::enum(NotificationChannel::class),
            'recipients' => 'array',
            'send_at' => 'nullable|datetime',
            'sent_at' => 'nullable|datetime',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'message' => 'required|string|max:255',
            'channels' => ['required', Rule::enum(NotificationChannel::class)],
        ];
    }
}
