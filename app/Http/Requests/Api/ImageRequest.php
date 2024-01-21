<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class ImageRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'description' => 'nullable|string',
            'filename' => 'string',
            'path' => 'string',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'image' => 'required|image|min:1|max:10000',
        ];
    }
}
