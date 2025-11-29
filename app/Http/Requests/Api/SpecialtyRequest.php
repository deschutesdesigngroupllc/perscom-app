<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class SpecialtyRequest extends Request
{
    /**
     * @return array<string, string>
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'abbreviation' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:65535',
            'order' => 'integer',
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
        ];
    }
}
