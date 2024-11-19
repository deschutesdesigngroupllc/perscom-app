<?php

declare(strict_types=1);

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
            'title' => 'string|max:255',
            'content' => 'string|max:65535',
            'color' => 'string|max:255',
            'global' => 'boolean',
            'enabled' => 'boolean',
            'expires_at' => 'nullable|date',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:65535',
            'color' => 'required|string|max:255',
        ];
    }
}
