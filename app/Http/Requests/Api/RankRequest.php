<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class RankRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string|max:65535',
            'abbreviation' => 'nullable|string|max:255',
            'paygrade' => 'nullable|string|max:255',
            'order' => 'integer',
            'updated_at' => 'date',
            'created_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
