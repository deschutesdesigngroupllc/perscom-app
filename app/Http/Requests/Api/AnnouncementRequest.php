<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AnnouncementRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'title' => 'string',
            'content' => 'string|in:info,success,warning,danger',
            'color' => 'string',
            'expires_at' => 'nullable|date',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'title' => 'required|string',
            'content' => 'required|string',
            'color' => 'required|string|in:info,success,warning,danger',
        ];
    }
}
