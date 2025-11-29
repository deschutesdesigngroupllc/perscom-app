<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class ImageRequest extends Request
{
    /**
     * @return array<string, string>
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:65535',
            'filename' => 'string|max:255',
            'model_type' => 'string|max:255',
            'model_id' => 'integer',
            'path' => 'string|max:255',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'required|image|min:1|max:10000',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        return data_forget($validated, 'image');
    }
}
