<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class DocumentRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'author_id' => 'integer|exists:users,id',
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:65535',
            'content' => 'string|max:65535',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:65535',
        ];
    }
}
