<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class MessageRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'message' => 'string|max:255',
            'channels' => 'array',
            'recipients' => 'array',
            'send_at' => 'nullable|datetime',
            'sent_at' => 'nullable|datetime',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'message' => 'required|string|max:255',
            'channels' => 'required|array',
        ];
    }
}
