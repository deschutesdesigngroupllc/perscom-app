<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class CategoryRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'description' => 'nullable|string',
            'resource' => 'string',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'resource' => 'required|string',
        ];
    }
}
