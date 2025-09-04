<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class IssuerRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
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
