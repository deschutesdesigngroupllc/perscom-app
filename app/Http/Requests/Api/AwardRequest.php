<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AwardRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'description' => 'nullable|string',
            'order' => 'integer',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }
}
