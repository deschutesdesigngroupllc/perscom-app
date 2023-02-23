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
            'description' => 'nullable|string',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
