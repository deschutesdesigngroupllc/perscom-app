<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class CategoryRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'description' => 'nullable|string',
            'resource' => 'string',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'resource' => 'required|string',
        ];
    }
}
