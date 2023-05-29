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
            'name' => 'sometimes|string',
            'slug' => 'sometimes|slug',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'success_message' => 'nullable|string',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required',
            'slug' => 'required',
        ];
    }
}
