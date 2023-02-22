<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class RankRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'name' => 'sometimes|string',
            'abbreviation' => 'nullable|string',
            'description' => 'nullable|string',
            'paygrade' => 'nullable|string',
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
