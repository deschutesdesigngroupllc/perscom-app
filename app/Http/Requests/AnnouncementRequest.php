<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class AnnouncementRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
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
        ];
    }
}
