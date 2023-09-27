<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class FormRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'slug' => 'slug',
            'success_message' => 'nullable|string',
            'is_public' => 'boolean',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'slug' => 'required|string',
            'is_public' => 'required|boolean',
        ];
    }
}
