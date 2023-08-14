<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class GroupRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'sometimes|string',
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
