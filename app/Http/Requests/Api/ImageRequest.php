<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class ImageRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'description' => 'nullable|string',
            'filename' => 'string',
            'path' => 'string',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'image' => 'required|image|min:1|max:10000',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        return data_forget($validated, 'image');
    }
}
