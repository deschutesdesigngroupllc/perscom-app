<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class CategoryRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:65535',
            'resource' => 'string|max:255',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'resource' => 'required|string|max:255',
        ];
    }
}
