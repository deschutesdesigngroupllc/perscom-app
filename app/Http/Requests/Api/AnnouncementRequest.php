<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AnnouncementRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'title' => 'string',
            'content' => 'string|in:info,success,warning,danger',
            'color' => 'string',
            'expires_at' => 'nullable|date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'title' => 'required|string',
            'content' => 'required|string',
            'color' => 'required|string|in:info,success,warning,danger',
        ];
    }
}
